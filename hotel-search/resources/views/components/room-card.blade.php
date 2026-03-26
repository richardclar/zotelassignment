@props([
    'roomTypeId',
    'roomTypeName',
    'roomTypeSlug',
    'available',
    'availableRooms',
    'mealPlan',
    'priceBreakdown',
    'nights'
])

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
    <div class="flex flex-col lg:flex-row">
        {{-- Room Image Section --}}
        <div class="lg:w-72 h-48 lg:h-auto flex-shrink-0 bg-gray-200">
            <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center relative overflow-hidden">
                @if($roomTypeSlug === 'deluxe')
                    <div class="absolute inset-0 opacity-20">
                        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <defs>
                                <pattern id="deluxe-pattern" patternUnits="userSpaceOnUse" width="20" height="20">
                                    <rect width="20" height="20" fill="#3B82F6" opacity="0.3"/>
                                    <circle cx="10" cy="10" r="2" fill="#1E40AF" opacity="0.5"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#deluxe-pattern)"/>
                        </svg>
                    </div>
                    <div class="relative z-10 text-center p-6">
                        <i class="fas fa-bed text-5xl text-blue-600 mb-2"></i>
                        <span class="text-blue-800 font-semibold text-sm">Premium Experience</span>
                    </div>
                @else
                    <div class="relative z-10 text-center p-6">
                        <i class="fas fa-bed text-5xl text-blue-500 mb-2"></i>
                        <span class="text-blue-700 font-medium text-sm">Comfortable Stay</span>
                    </div>
                @endif
                
                @if($available)
                    <div class="absolute top-3 left-3">
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                            Available
                        </span>
                    </div>
                @else
                    <div class="absolute top-3 left-3">
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                            Sold Out
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Room Details Section --}}
        <div class="flex-1 p-5 lg:p-6 flex flex-col">
            <div class="flex-1">
                {{-- Room Header --}}
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $roomTypeName }}</h3>
                        <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-user text-gray-400"></i>
                                Up to 3 guests
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-door-open text-gray-400"></i>
                                {{ $availableRooms }} left
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Amenities --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-wifi text-gray-400"></i> WiFi
                    </span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-tv text-gray-400"></i> TV
                    </span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-snowflake text-gray-400"></i> AC
                    </span>
                    @if($roomTypeSlug === 'deluxe')
                        <span class="bg-blue-100 text-blue-600 text-xs px-2.5 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-hot-tub-person text-blue-400"></i> Jacuzzi
                        </span>
                        <span class="bg-blue-100 text-blue-600 text-xs px-2.5 py-1 rounded-full flex items-center gap-1">
                            <i class="fas fa-umbrella-beach text-blue-400"></i> Balcony
                        </span>
                    @endif
                </div>

                {{-- Meal Plan --}}
                <div class="flex items-center gap-2 mb-4 text-sm">
                    <i class="fas fa-utensils text-gray-400"></i>
                    <span class="text-gray-600">{{ $mealPlan }}</span>
                </div>

                {{-- Price Breakdown --}}
                @if($priceBreakdown)
                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500 text-xs">Base Price</span>
                                <p class="font-semibold text-gray-900">₹{{ number_format($priceBreakdown['base_price'], 0) }}</p>
                            </div>
                            @if($priceBreakdown['meal_price'] > 0)
                                <div>
                                    <span class="text-gray-500 text-xs">Meal Plan</span>
                                    <p class="font-semibold text-gray-900">₹{{ number_format($priceBreakdown['meal_price'], 0) }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="text-gray-500 text-xs">Subtotal</span>
                                <p class="font-semibold text-gray-900">₹{{ number_format($priceBreakdown['subtotal'], 0) }}</p>
                            </div>
                            @if($priceBreakdown['discount'] > 0)
                                <div class="text-green-600">
                                    <span class="text-green-500 text-xs">Discount</span>
                                    <p class="font-semibold">-₹{{ number_format($priceBreakdown['discount'], 0) }}</p>
                                </div>
                            @else
                                <div>
                                    <span class="text-gray-500 text-xs">&nbsp;</span>
                                    <p class="font-semibold text-gray-400">No discount</p>
                                </div>
                            @endif
                        </div>

                        {{-- Applied Discounts --}}
                        @if(!empty($priceBreakdown['applied_discounts']))
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($priceBreakdown['applied_discounts'] as $discount)
                                        <span class="bg-green-100 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
                                            <i class="fas fa-tag"></i>
                                            {{ $discount['name'] }} (-{{ $discount['value'] }}%)
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Price & CTA Section --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 pt-4 border-t border-gray-100">
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ $nights }} night{{ $nights > 1 ? 's' : '' }}</div>
                    @if($available && $priceBreakdown)
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-bold text-gray-900">₹{{ number_format($priceBreakdown['final_price'], 0) }}</span>
                            <span class="text-sm text-gray-500">total</span>
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            ₹{{ number_format($priceBreakdown['price_per_night'], 0) }} / night
                        </div>
                    @else
                        <div class="text-2xl font-bold text-gray-400">N/A</div>
                        <div class="text-sm text-gray-400">Not available</div>
                    @endif
                </div>

                <div class="w-full sm:w-auto">
                    @if($available)
                        <button class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3 px-8 rounded-xl transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-calendar-check"></i>
                            Book Now
                        </button>
                    @else
                        <button disabled class="w-full sm:w-auto bg-gray-200 text-gray-400 font-semibold py-3 px-8 rounded-xl cursor-not-allowed">
                            Not Available
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
