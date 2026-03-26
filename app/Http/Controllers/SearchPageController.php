<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SearchRequestDTO;
use App\Enums\MealPlanType;
use App\Interfaces\SearchServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class SearchPageController extends Controller
{
    public function __construct(
        private readonly SearchServiceInterface $searchService
    ) {
    }

    public function index(): View
    {
        $tomorrow = Carbon::tomorrow();
        $dayAfter = Carbon::tomorrow()->addDay();

        return view('search.index', [
            'defaultCheckIn' => $tomorrow->format('Y-m-d'),
            'defaultCheckOut' => $dayAfter->format('Y-m-d'),
            'minDate' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    public function search(Request $request): View
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date', 'date_format:Y-m-d'],
            'check_out' => ['required', 'date', 'date_format:Y-m-d', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:3'],
            'meal_plan' => ['required', 'in:room_only,breakfast'],
        ]);

        $results = collect();
        $errors = null;
        $meta = null;

        try {
            $searchRequest = new SearchRequestDTO(
                checkInDate: Carbon::parse($validated['check_in']),
                checkOutDate: Carbon::parse($validated['check_out']),
                adults: (int) $validated['adults'],
                mealPlan: MealPlanType::from($validated['meal_plan'])
            );

            $results = $this->searchService->search($searchRequest);
            $meta = [
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'adults' => (int) $validated['adults'],
                'meal_plan' => $validated['meal_plan'],
                'nights' => $searchRequest->getNights(),
                'total_results' => $results->count(),
                'available_count' => $results->where('available', true)->count(),
            ];
        } catch (InvalidArgumentException $e) {
            $errors = $e->getMessage();
        }

        return view('search.index', [
            'defaultCheckIn' => $validated['check_in'],
            'defaultCheckOut' => $validated['check_out'],
            'selectedAdults' => (int) $validated['adults'],
            'selectedMealPlan' => $validated['meal_plan'],
            'minDate' => Carbon::today()->format('Y-m-d'),
            'results' => $results,
            'meta' => $meta,
            'errors' => $errors,
        ]);
    }
}
