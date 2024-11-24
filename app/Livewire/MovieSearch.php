<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OmdbService;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class MovieSearch extends Component
{
    use WithPagination;

    public string $search = '';
    public ?array $results = null;
    public int $page = 1;
    public bool $isLoading = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function searchMovies()
    {
        if (strlen($this->search) < 3) {
            return;
        }

        try {
            $this->isLoading = true;
            $omdb = new OmdbService();
            $this->results = $omdb->searchMovies($this->search, $this->page);
        } catch (\Exception $e) {
            $this->addError('search', $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function loadMore()
    {
        $this->page++;
        $this->searchMovies();
    }

    public function render()
    {
        return view('livewire.movie-search');
    }
}
