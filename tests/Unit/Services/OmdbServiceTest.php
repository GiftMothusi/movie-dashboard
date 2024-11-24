<?php

namespace Tests\Unit\Services;

use App\Services\OmdbService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OmdbServiceTest extends TestCase
{
    private OmdbService $omdbService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->omdbService = new OmdbService();
    }

    /** @test */
    public function it_can_search_movies()
    {
        // Mock HTTP response
        Http::fake([
            '*' => Http::response([
                'Search' => [
                    [
                        'Title' => 'Iron Man',
                        'Year' => '2008',
                        'imdbID' => 'tt0371746',
                        'Type' => 'movie',
                        'Poster' => 'https://example.com/poster.jpg'
                    ]
                ],
                'totalResults' => '1',
                'Response' => 'True'
            ], 200)
        ]);

        $result = $this->omdbService->searchMovies('Iron Man');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Search', $result);
        $this->assertEquals('Iron Man', $result['Search'][0]['Title']);
    }

    /** @test */
    public function it_handles_failed_search_requests()
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'False',
                'Error' => 'Movie not found!'
            ], 200)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error searching movies: Movie not found!');

        $this->omdbService->searchMovies('NonexistentMovie123');
    }

    /** @test */
    public function it_can_get_movie_details()
    {
        Http::fake([
            '*' => Http::response([
                'Title' => 'Iron Man',
                'Year' => '2008',
                'Rated' => 'PG-13',
                'Released' => '02 May 2008',
                'Runtime' => '126 min',
                'Genre' => 'Action, Adventure, Sci-Fi',
                'Director' => 'Jon Favreau',
                'Plot' => 'Test plot',
                'Response' => 'True'
            ], 200)
        ]);

        $result = $this->omdbService->getMovie('tt0371746');

        $this->assertIsArray($result);
        $this->assertEquals('Iron Man', $result['Title']);
        $this->assertEquals('2008', $result['Year']);
    }

    /** @test */
    public function it_handles_failed_movie_details_requests()
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'False',
                'Error' => 'Invalid IMDb ID'
            ], 200)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error fetching movie: Invalid IMDb ID');

        $this->omdbService->getMovie('invalid-id');
    }

    /** @test */
    public function it_caches_movie_search_results()
    {
        // Clear the cache first
        Cache::flush();

        $mockResponse = [
            'Search' => [
                [
                    'Title' => 'Iron Man',
                    'Year' => '2008',
                    'imdbID' => 'tt0371746'
                ]
            ],
            'Response' => 'True'
        ];

        Http::fake([
            '*' => Http::response($mockResponse, 200)
        ]);

        // First call should hit the API
        $firstResult = $this->omdbService->searchMovies('Iron Man');

        // Modify the fake response to verify we're getting cached result
        Http::fake([
            '*' => Http::response([
                'Search' => [
                    [
                        'Title' => 'Different Movie',
                        'Year' => '2020',
                        'imdbID' => 'tt9999999'
                    ]
                ],
                'Response' => 'True'
            ], 200)
        ]);

        // Second call should return cached result
        $secondResult = $this->omdbService->searchMovies('Iron Man');

        $this->assertEquals($firstResult, $secondResult);
    }

    /** @test */
    public function it_can_get_trending_movies()
    {
        Cache::put('trending_movies', [
            'tt0371746' => 5, // Iron Man
            'tt0848228' => 3, // The Avengers
        ], now()->addDay());

        Http::fake([
            '*' => Http::sequence()
                ->push([
                    'Title' => 'Iron Man',
                    'Year' => '2008',
                    'Response' => 'True'
                ])
                ->push([
                    'Title' => 'The Avengers',
                    'Year' => '2012',
                    'Response' => 'True'
                ])
        ]);

        $trending = $this->omdbService->getTrendingMovies();

        $this->assertIsArray($trending);
        $this->assertCount(2, $trending);
        $this->assertEquals('Iron Man', $trending[0]['Title']);
        $this->assertEquals('The Avengers', $trending[1]['Title']);
    }

    /** @test */
    public function it_handles_network_errors()
    {
        Http::fake([
            '*' => Http::response(null, 500)
        ]);

        $this->expectException(\Exception::class);
        $this->omdbService->searchMovies('Iron Man');
    }

    /** @test */
    public function it_handles_invalid_api_key()
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'False',
                'Error' => 'Invalid API key!'
            ], 401)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error searching movies: Invalid API key!');

        $this->omdbService->searchMovies('Iron Man');
    }
}
