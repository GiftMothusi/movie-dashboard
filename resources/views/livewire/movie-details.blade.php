<div class="max-w-4xl mx-auto">
    @if($isLoading)
        <div class="flex items-center justify-center min-h-[400px]">
            <div class="text-gray-500">Loading movie details...</div>
        </div>
    @elseif($movie)
        <div class="overflow-hidden bg-white rounded-lg shadow-lg">
            <div class="md:flex">
                <div class="md:w-1/3">
                    @if($movie['Poster'] !== 'N/A')
                        <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}" class="object-cover w-full h-full">
                    @else
                        <div class="flex items-center justify-center w-full h-full min-h-[400px] bg-gray-200">
                            <span class="text-gray-400">No Poster Available</span>
                        </div>
                    @endif
                </div>
                <div class="p-6 md:w-2/3">
                    <h1 class="mb-4 text-3xl font-bold">{{ $movie['Title'] }} ({{ $movie['Year'] }})</h1>

                    <div class="grid gap-4 mb-6">
                        <div>
                            <h2 class="font-semibold text-gray-600">Rating</h2>
                            <div class="flex space-x-2">
                                @foreach($movie['Ratings'] ?? [] as $rating)
                                    <span class="px-2 py-1 text-sm bg-gray-100 rounded">
                                        {{ $rating['Source'] }}: {{ $rating['Value'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h2 class="font-semibold text-gray-600">Plot</h2>
                            <p class="text-gray-700">{{ $movie['Plot'] }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h2 class="font-semibold text-gray-600">Director</h2>
                                <p class="text-gray-700">{{ $movie['Director'] }}</p>
                            </div>

                            <div>
                                <h2 class="font-semibold text-gray-600">Genre</h2>
                                <p class="text-gray-700">{{ $movie['Genre'] }}</p>
                            </div>

                            <div>
                                <h2 class="font-semibold text-gray-600">Runtime</h2>
                                <p class="text-gray-700">{{ $movie['Runtime'] }}</p>
                            </div>

                            <div>
                                <h2 class="font-semibold text-gray-600">Released</h2>
                                <p class="text-gray-700">{{ $movie['Released'] }}</p>
                            </div>
                        </div>

                        <div>
                            <h2 class="font-semibold text-gray-600">Cast</h2>
                            <p class="text-gray-700">{{ $movie['Actors'] }}</p>
                        </div>
                    </div>

                    <a
                        href="{{ route('movies.index') }}"
                        class="inline-block px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                    >
                        Back to Search
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="p-4 text-red-500">
            @error('movie')
                {{ $message }}
            @else
                Movie not found
            @enderror
        </div>
    @endif
</div>
