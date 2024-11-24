<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OmdbService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class MovieDetails extends Component
{
    public string $imdbId = '';
    public ?array $movie = null;
    public bool $isLoading = false;

    public function mount(string $imdbId)
    {
        $this->imdbId = $imdbId;
        $this->loadMovie();
    }

    public function loadMovie()
    {
        try {
            $this->isLoading = true;
            $omdb = new OmdbService();
            $this->movie = $omdb->getMovie($this->imdbId);
        } catch (\Exception $e) {
            $this->addError('movie', $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.movie-details');
    }
}
