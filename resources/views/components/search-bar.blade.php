@props([
    'defaultCheckIn',
    'defaultCheckOut',
    'selectedAdults' => 2,
    'minDate'
])

<form action="{{ route('search.submit') }}" method="GET" 
    class="bg-white rounded-2xl shadow-lg p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar-check text-blue-500 mr-1"></i> Check-in
            </label>
            <input type="date" name="check_in" 
                value="{{ $defaultCheckIn }}" 
                min="{{ $minDate }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-700"
                required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Check-out
            </label>
            <input type="date" name="check_out" 
                value="{{ $defaultCheckOut }}" 
                min="{{ $minDate }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-700"
                required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-users text-blue-500 mr-1"></i> Adults
            </label>
            <select name="adults" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-gray-700 bg-white">
                <option value="1" {{ $selectedAdults == 1 ? 'selected' : '' }}>1 Adult</option>
                <option value="2" {{ $selectedAdults == 2 ? 'selected' : '' }}>2 Adults</option>
                <option value="3" {{ $selectedAdults == 3 ? 'selected' : '' }}>3 Adults</option>
                <option value="4" {{ $selectedAdults == 4 ? 'selected' : '' }}>4 Adults</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </button>
        </div>
    </div>
</form>
