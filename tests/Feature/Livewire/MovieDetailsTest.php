<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MovieDetails;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class MovieDetailsTest extends TestCase
{
    /** @test */
    public function can_view_movie_details()
    {
        Http::fake([
            '*' => Http::response([
                'Title' => 'Iron Man',
                'Year' => '2008',
                'Plot' => 'Test plot',
                'Response' => 'True'
            ], 200)
        ]);

        Livewire::test(MovieDetails::class, ['imdbId' => 'tt0371746'])
            ->assertSet('isLoading', false)
            ->assertSet('movie.Title', 'Iron Man')
            ->assertSet('movie.Year', '2008')
            ->assertSuccessful();
    }

    /** @test */
    public function shows_error_for_invalid_movie_id()
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'False',
                'Error' => 'Invalid IMDb ID'
            ], 200)
        ]);

        Livewire::test(MovieDetails::class, ['imdbId' => 'invalid-id'])
            ->assertHasErrors('movie');
    }
}
