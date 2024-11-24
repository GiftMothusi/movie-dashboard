<div class="space-y-4">
    <h2 class="text-2xl font-bold">Trending Movies</h2>

    @if($isLoading)
        <div class="text-gray-500">Loading trending movies...</div>
    @elseif(count($trendingMovies) > 0)
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($trendingMovies as $movie)
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
                        <p class="mt-2 text-gray-600">Rating: {{ $movie['imdbRating'] }}</p>
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
    @else
        <div class="text-gray-500">No trending movies available.</div>
    @endif
</div>
