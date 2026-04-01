<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SearchRequestDTO;
use App\Interfaces\SearchServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class SearchPageController extends Controller
{
    public function __construct(
        private readonly SearchServiceInterface $searchService
    ) {}

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
            'adults' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $results = collect();
        $errors = null;
        $meta = null;

        try {
            $searchRequest = new SearchRequestDTO(
                checkInDate: Carbon::parse($validated['check_in']),
                checkOutDate: Carbon::parse($validated['check_out']),
                adults: (int) $validated['adults']
            );

            $results = $this->searchService->search($searchRequest);

            $allRatePlans = $results->flatMap(fn ($r) => collect($r->ratePlans));

            $meta = [
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'adults' => (int) $validated['adults'],
                'nights' => $searchRequest->getNights(),
                'total_room_types' => $results->count(),
                'available_count' => $allRatePlans->where('available', true)->count(),
            ];
        } catch (InvalidArgumentException $e) {
            $errors = $e->getMessage();
        }

        return view('search.index', [
            'defaultCheckIn' => $validated['check_in'],
            'defaultCheckOut' => $validated['check_out'],
            'selectedAdults' => (int) $validated['adults'],
            'minDate' => Carbon::today()->format('Y-m-d'),
            'results' => $results,
            'meta' => $meta,
            'errors' => $errors,
        ]);
    }
}
