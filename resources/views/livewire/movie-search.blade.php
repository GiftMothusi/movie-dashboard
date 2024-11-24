<div class="space-y-4">
    <div class="flex items-center space-x-4">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                wire:keydown.enter="searchMovies"
                placeholder="Search for movies..."
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>
        <button
            wire:click="searchMovies"
            class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            Search
        </button>
    </div>

    @error('search')
        <div class="text-red-500">{{ $message }}</div>
    @enderror

    <div wire:loading wire:target="searchMovies" class="text-gray-500">
        Searching...
    </div>

    @if($results && isset($results['Search']))
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($results['Search'] as $movie)
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    @if($movie['Poster'] !== 'N/A')
                        <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="object-cover w-full h-64">
                    @else
                        <div class="flex items-center justify-center w-full h-64 bg-gray-200">
                            <span class="text-gray-400">No Poster Available</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="mb-2 text-xl font-semibold">{{ $movie['Title'] }}</h3>
                        <p class="text-gray-600">Year: {{ $movie['Year'] }}</p>
                        <a
                            href="{{ route('movies.show', $movie['imdbID']) }}"
                            class="inline-block px-4 py-2 mt-4 text-sm text-white bg-blue-600 rounded hover:bg-blue-700"
                        >
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if(isset($results['totalResults']) && $results['totalResults'] > count($results['Search']))
            <div class="mt-4 text-center">
                <button
                    wire:click="loadMore"
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                >
                    Load More
                </button>
            </div>
        @endif
    @endif
</div>
