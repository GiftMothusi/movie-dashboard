<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MovieSearch;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class MovieSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function search_page_contains_livewire_component()
    {
        $this->get('/')
            ->assertSuccessful()
            ->assertSeeLivewire('movie-search');
    }

    /** @test */
    public function can_search_movies()
    {
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

        Livewire::test(MovieSearch::class)
            ->set('search', 'Iron Man')
            ->call('searchMovies')
            ->assertSet('isLoading', false)
            ->assertHasNoErrors();
    }

    /** @test */
    public function shows_error_message_when_api_fails()
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'False',
                'Error' => 'Movie not found!'
            ], 200)
        ]);

        Livewire::test(MovieSearch::class)
            ->set('search', 'NonexistentMovie123')
            ->call('searchMovies')
            ->assertHasErrors('search');
    }

    /** @test */
    public function does_not_search_with_short_query()
    {
        Livewire::test(MovieSearch::class)
            ->set('search', 'ab')
            ->call('searchMovies')
            ->assertSet('results', null);
    }
}
