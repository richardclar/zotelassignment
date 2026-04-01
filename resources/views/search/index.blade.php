@extends('layouts.app')

@section('title', 'Search Hotels - Zotel Project')

@section('content')
<div class="min-h-screen flex flex-col bg-gray-50">
    {{-- Full Width Header --}}
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i class="fas fa-hotel text-white text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-white">Zotel Project</span>
                        <span class="block text-blue-200 text-xs">Premium Hotel Experience</span>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-blue-100 text-sm">
                    <span><i class="fas fa-phone mr-2"></i>+91 98765 43210</span>
                    <span><i class="fas fa-envelope mr-2"></i>info@zotelproject.com</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Search Section --}}
    <div class="bg-gradient-to-b from-blue-50 to-blue-100/50 py-10 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-6">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    Find Your Perfect Stay
                </h1>
                <p class="text-gray-600">
                    Search available rooms and get the best prices for your next trip
                </p>
            </div>

            <x-search-bar 
                :defaultCheckIn="$defaultCheckIn"
                :defaultCheckOut="$defaultCheckOut"
                :selectedAdults="$selectedAdults ?? 2"
                :selectedMealPlan="$selectedMealPlan ?? 'room_only'"
                :minDate="$minDate"
            />
        </div>
    </div>

    {{-- Error Message --}}
    @if(isset($errors) && $errors instanceof \Illuminate\Support\MessageBag && $errors->any())
        <div class="max-w-5xl mx-auto px-4 mt-6">
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
                <span class="font-medium">{{ $errors->first() }}</span>
            </div>
        </div>
    @endif

    {{-- Results Section --}}
    <main class="flex-1 max-w-5xl mx-auto w-full px-4 py-8">
        @if(isset($meta) && $meta)
            {{-- Search Summary --}}
            <x-search-summary 
                :checkIn="$meta['check_in']"
                :checkOut="$meta['check_out']"
                :nights="$meta['nights']"
                :adults="$meta['adults']"
                :availableCount="$meta['available_count']"
            />

            {{-- Results Header --}}
            <div class="mt-6 mb-4">
                <h2 class="text-xl font-bold text-gray-900">
                    @if($results->isNotEmpty())
                        {{ $results->count() }} Room Type{{ $results->count() > 1 ? 's' : '' }} Available
                    @else
                        No Rooms Available
                    @endif
                </h2>
            </div>

            {{-- Results List --}}
            @if($results->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bed text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Rooms Available</h3>
                    <p class="text-gray-500 mb-6">We couldn't find any available rooms for your selected dates.</p>
                    <p class="text-sm text-gray-400">Try adjusting your search criteria or selecting different dates.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($results as $result)
                        <x-room-card 
                            :roomTypeId="$result->roomTypeId"
                            :roomTypeName="$result->roomTypeName"
                            :roomTypeSlug="$result->roomTypeSlug"
                            :maxOccupancy="$result->maxOccupancy"
                            :ratePlans="$result->ratePlans"
                            :nights="$meta['nights']"
                        />
                    @endforeach
                </div>
            @endif

            {{-- Search Tips --}}
            @if($results->filter(fn($r) => $r->hasAvailableRatePlans())->count() < $results->count())
                <div class="mt-8 bg-blue-50 rounded-2xl p-6 border border-blue-100">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-blue-500"></i>
                        Tips for Better Availability
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-1"></i>
                            <span>Try adjusting your dates - weekdays often have better availability</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-1"></i>
                            <span>Consider shorter stays to find more options</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-1"></i>
                            <span>Check back later - availability changes frequently</span>
                        </li>
                    </ul>
                </div>
            @endif

        @else
            {{-- Empty State - Before Search --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-blue-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Search for Available Rooms</h3>
                <p class="text-gray-500 mb-6">Enter your dates and preferences above to see available rooms and prices.</p>
                
                <div class="flex flex-wrap justify-center gap-3 text-sm text-gray-500">
                    <span class="bg-gray-100 px-3 py-1.5 rounded-full">
                        <i class="fas fa-moon text-gray-400 mr-1"></i> 30+ nights available
                    </span>
                    <span class="bg-gray-100 px-3 py-1.5 rounded-full">
                        <i class="fas fa-percent text-gray-400 mr-1"></i> Up to 25% off
                    </span>
                    <span class="bg-gray-100 px-3 py-1.5 rounded-full">
                        <i class="fas fa-utensils text-gray-400 mr-1"></i> Free cancellation
                    </span>
                </div>
            </div>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                <p>&copy; 2026 Zotel Project Hotel. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-blue-600 transition">About</a>
                    <a href="#" class="hover:text-blue-600 transition">Contact</a>
                    <a href="#" class="hover:text-blue-600 transition">Privacy</a>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkInInput = document.querySelector('input[name="check_in"]');
        const checkOutInput = document.querySelector('input[name="check_out"]');
        
        if (checkInInput && checkOutInput) {
            checkInInput.addEventListener('change', function() {
                const checkIn = new Date(this.value);
                checkIn.setDate(checkIn.getDate() + 1);
                const minCheckOut = checkIn.toISOString().split('T')[0];
                checkOutInput.min = minCheckOut;
                
                if (checkOutInput.value <= this.value) {
                    checkOutInput.value = minCheckOut;
                }
            });
        }
    });
</script>
@endpush
