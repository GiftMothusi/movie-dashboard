<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TrendingMovies;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class TrendingMoviesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // Clear cache before each test
    }

    /** @test */
    public function trending_page_contains_livewire_component()
    {
        $this->get('/trending')
            ->assertSuccessful()
            ->assertSeeLivewire('trending-movies');
    }

    /** @test */
    public function can_load_trending_movies()
    {
        // Set up trending movies in cache
        Cache::put('trending_movies', [
            'tt0371746' => 5, // Iron Man
            'tt0848228' => 3, // The Avengers
        ], now()->addDay());

        // Mock API responses for each movie
        Http::fake([
            '*' => Http::sequence()
                ->push([
                    'Title' => 'Iron Man',
                    'Year' => '2008',
                    'imdbRating' => '7.9',
                    'Response' => 'True'
                ])
                ->push([
                    'Title' => 'The Avengers',
                    'Year' => '2012',
                    'imdbRating' => '8.0',
                    'Response' => 'True'
                ])
        ]);

        Livewire::test(TrendingMovies::class)
            ->assertSet('isLoading', false)
            ->assertCount('trendingMovies', 2)
            ->assertSee('Iron Man')
            ->assertSee('The Avengers');
    }

    /** @test */
    public function handles_empty_trending_movies()
    {
        // Ensure no trending movies in cache
        Cache::forget('trending_movies');

        Livewire::test(TrendingMovies::class)
            ->assertSet('isLoading', false)
            ->assertCount('trendingMovies', 0)
            ->assertSee('No trending movies available');
    }

    /** @test */
    public function handles_api_failure_for_trending_movies()
    {
        // Set up trending movies in cache
        Cache::put('trending_movies', [
            'tt0371746' => 5, // Iron Man
        ], now()->addDay());

        // Mock API failure
        Http::fake([
            '*' => Http::response([
                'Response' => 'False',
                'Error' => 'Error occurred'
            ], 500)
        ]);

        Livewire::test(TrendingMovies::class)
            ->assertSet('isLoading', false)
            ->assertCount('trendingMovies', 0)
            ->assertHasErrors('trending');
    }

    /** @test */
    public function shows_loading_state_while_fetching_trending_movies()
    {
        Cache::put('trending_movies', [
            'tt0371746' => 5, // Iron Man
        ], now()->addDay());

        // Add a delay to the HTTP response to simulate loading
        Http::fake([
            '*' => Http::response([
                'Title' => 'Iron Man',
                'Year' => '2008',
                'Response' => 'True'
            ])
        ]);

        Livewire::test(TrendingMovies::class)
            ->assertSet('isLoading', true);
    }

    /** @test */
    public function trending_movies_are_ordered_by_view_count()
    {
        // Set up trending movies in cache with different view counts
        Cache::put('trending_movies', [
            'tt0848228' => 10, // The Avengers (most viewed)
            'tt0371746' => 5,  // Iron Man
            'tt1228705' => 3,  // Iron Man 2
        ], now()->addDay());

        Http::fake([
            '*' => Http::sequence()
                ->push([
                    'Title' => 'The Avengers',
                    'Year' => '2012',
                    'Response' => 'True'
                ])
                ->push([
                    'Title' => 'Iron Man',
                    'Year' => '2008',
                    'Response' => 'True'
                ])
                ->push([
                    'Title' => 'Iron Man 2',
                    'Year' => '2010',
                    'Response' => 'True'
                ])
        ]);

        $component = Livewire::test(TrendingMovies::class);

        // Assert the first movie is The Avengers (most viewed)
        $component->assertSeeInOrder(['The Avengers', 'Iron Man', 'Iron Man 2']);
    }

    /** @test */
    public function trending_movies_limit_is_respected()
    {
        // Set up more trending movies than the default limit
        $trendingMovies = array_fill(0, 15, 1); // 15 movies
        $trendingMovies = array_combine(
            array_map(fn($i) => "tt{$i}", range(1000, 1014)),
            $trendingMovies
        );

        Cache::put('trending_movies', $trendingMovies, now()->addDay());

        // Mock responses for all possible movies
        Http::fake([
            '*' => Http::response([
                'Title' => 'Movie Title',
                'Year' => '2020',
                'Response' => 'True'
            ])
        ]);

        $component = Livewire::test(TrendingMovies::class);

        // Assert we only get 10 movies (default limit)
        $component->assertCount('trendingMovies', 10);
    }

    /** @test */
    public function refreshes_trending_movies_list()
    {
        Cache::put('trending_movies', [
            'tt0371746' => 5, // Iron Man
        ], now()->addDay());

        Http::fake([
            '*' => Http::response([
                'Title' => 'Iron Man',
                'Year' => '2008',
                'Response' => 'True'
            ])
        ]);

        Livewire::test(TrendingMovies::class)
            ->call('loadTrendingMovies')
            ->assertSet('isLoading', false)
            ->assertCount('trendingMovies', 1);
    }

    /** @test */
    public function caches_trending_movies_results()
    {
        $movieData = [
            'Title' => 'Iron Man',
            'Year' => '2008',
            'Response' => 'True'
        ];

        Cache::put('trending_movies', [
            'tt0371746' => 5, // Iron Man
        ], now()->addDay());

        // Count HTTP requests
        $requestCount = 0;
        Http::fake([
            '*' => function() use (&$requestCount, $movieData) {
                $requestCount++;
                return Http::response($movieData);
            }
        ]);

        // First load
        Livewire::test(TrendingMovies::class);

        // Second load should use cached data
        Livewire::test(TrendingMovies::class);

        // Assert that only one HTTP request was made despite two loads
        $this->assertEquals(1, $requestCount, 'Cache is not working as expected');
    }
}
