<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\DTO\SearchRequestDTO;
use Illuminate\Support\Collection;

interface DiscountServiceInterface
{
    public function calculateDiscounts(
        SearchRequestDTO $request,
        float $subtotal,
        int $roomTypeId,
        ?int $ratePlanTypeId = null
    ): array;

    public function getApplicableDiscounts(
        SearchRequestDTO $request,
        int $roomTypeId,
        ?int $ratePlanTypeId = null
    ): Collection;
}
