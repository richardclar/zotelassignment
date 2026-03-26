<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\DiscountDTO;
use App\DTO\SearchRequestDTO;
use App\Interfaces\DiscountServiceInterface;
use App\Models\DiscountRule;
use App\Models\DiscountType;
use Illuminate\Support\Collection;

class DiscountService implements DiscountServiceInterface
{
    public function calculateDiscounts(
        SearchRequestDTO $request,
        float $subtotal,
        int $roomTypeId
    ): array {
        $applicableDiscounts = $this->getApplicableDiscounts($request, $roomTypeId);
        $appliedDiscounts = [];
        $totalDiscount = 0.0;

        foreach ($applicableDiscounts as $discountRule) {
            $discountType = $discountRule->discountType;
            $amount = $this->calculateDiscountAmount($discountType, $subtotal);

            if ($amount > 0) {
                $appliedDiscounts[] = new DiscountDTO(
                    name: $discountType->name,
                    slug: $discountType->slug,
                    type: $discountType->discount_type,
                    value: (float) $discountType->discount_value,
                    amount: $amount
                );
                $totalDiscount += $amount;

                if (!$discountType->is_stackable) {
                    break;
                }
            }
        }

        return [
            'discounts' => $appliedDiscounts,
            'total_discount' => $totalDiscount,
        ];
    }

    public function getApplicableDiscounts(SearchRequestDTO $request, int $roomTypeId): Collection
    {
        $nights = $request->getNights();
        $daysBeforeCheckin = $request->getDaysBeforeCheckin();

        return DiscountRule::query()
            ->where('is_active', true)
            ->where(function ($query) use ($roomTypeId) {
                $query->whereNull('room_type_id')
                    ->orWhere('room_type_id', $roomTypeId);
            })
            ->where(function ($query) use ($nights) {
                $query->where('min_nights', '<=', $nights)
                    ->orWhereNull('min_nights');
            })
            ->where(function ($query) use ($nights) {
                $query->whereNull('max_nights')
                    ->orWhere('max_nights', '>=', $nights);
            })
            ->where(function ($query) use ($daysBeforeCheckin) {
                $query->whereNull('min_days_before_checkin')
                    ->orWhere('min_days_before_checkin', '<=', $daysBeforeCheckin);
            })
            ->where(function ($query) use ($daysBeforeCheckin) {
                $query->whereNull('max_days_before_checkin')
                    ->orWhere('max_days_before_checkin', '>=', $daysBeforeCheckin);
            })
            ->where(function ($query) use ($request) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $request->checkInDate);
            })
            ->where(function ($query) use ($request) {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', $request->checkInDate);
            })
            ->with('discountType')
            ->get();
    }

    private function calculateDiscountAmount(DiscountType $discountType, float $subtotal): float
    {
        if ($discountType->isPercentage()) {
            return ($subtotal * (float) $discountType->discount_value) / 100;
        }

        return (float) $discountType->discount_value;
    }
}
