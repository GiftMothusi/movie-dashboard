<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\MovieSearch;
use App\Livewire\MovieDetails;
use App\Livewire\TrendingMovies;

Route::get('/', MovieSearch::class)->name('movies.index');
Route::get('/movies/{imdbId}', MovieDetails::class)->name('movies.show');
Route::get('/trending', TrendingMovies::class)->name('movies.trending');
