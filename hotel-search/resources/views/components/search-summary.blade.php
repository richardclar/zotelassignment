@props([
    'checkIn',
    'checkOut',
    'nights',
    'adults',
    'availableCount'
])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 px-5 py-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <div class="flex items-center gap-2 text-gray-700">
                <i class="fas fa-calendar-day text-blue-500"></i>
                <span class="font-medium">{{ \Carbon\Carbon::parse($checkIn)->format('M d') }}</span>
                <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                <span class="font-medium">{{ \Carbon\Carbon::parse($checkOut)->format('M d, Y') }}</span>
            </div>
            <span class="text-gray-400">|</span>
            <div class="flex items-center gap-1 text-gray-600">
                <i class="fas fa-moon text-blue-500"></i>
                <span>{{ $nights }} night{{ $nights > 1 ? 's' : '' }}</span>
            </div>
            <span class="text-gray-400">|</span>
            <div class="flex items-center gap-1 text-gray-600">
                <i class="fas fa-user text-blue-500"></i>
                <span>{{ $adults }} adult{{ $adults > 1 ? 's' : '' }}</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            @if($availableCount > 0)
                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5">
                    <i class="fas fa-check-circle"></i>
                    {{ $availableCount }} room{{ $availableCount > 1 ? 's' : '' }} available
                </span>
            @else
                <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1.5 rounded-full flex items-center gap-1.5">
                    <i class="fas fa-times-circle"></i>
                    No availability
                </span>
            @endif
        </div>
    </div>
</div>
