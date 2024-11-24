<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OmdbService;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class TrendingMovies extends Component
{
    public array $trendingMovies = [];
    public bool $isLoading = false;

    public function mount()
    {
        $this->loadTrendingMovies();
    }

    public function loadTrendingMovies()
    {
        try {
            $this->isLoading = true;
            $omdb = new OmdbService();
            $this->trendingMovies = $omdb->getTrendingMovies(10);
        } catch (\Exception $e) {
            $this->addError('trending', $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.trending-movies');
    }
}
