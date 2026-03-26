<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\SearchRequestDTO;
use App\Enums\MealPlanType;
use App\Http\Controllers\Controller;
use App\Interfaces\SearchServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchServiceInterface $searchService
    ) {
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date', 'date_format:Y-m-d'],
            'check_out' => ['required', 'date', 'date_format:Y-m-d', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:3'],
            'meal_plan' => ['required', Rule::enum(MealPlanType::class)],
        ]);

        try {
            $searchRequest = new SearchRequestDTO(
                checkInDate: Carbon::parse($validated['check_in']),
                checkOutDate: Carbon::parse($validated['check_out']),
                adults: (int) $validated['adults'],
                mealPlan: MealPlanType::from($validated['meal_plan'])
            );

            $results = $this->searchService->search($searchRequest);

            return response()->json([
                'success' => true,
                'data' => $results->values()->toArray(),
                'meta' => [
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out'],
                    'adults' => (int) $validated['adults'],
                    'meal_plan' => $validated['meal_plan'],
                    'nights' => $searchRequest->getNights(),
                    'total_results' => $results->count(),
                    'available_count' => $results->where('available', true)->count(),
                ],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'An error occurred while processing your search.',
                ],
            ], 500);
        }
    }
}
