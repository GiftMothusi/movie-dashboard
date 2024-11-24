<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class OmdbService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.omdb.key');
        $this->baseUrl = config('services.omdb.url');
    }

    /**
     * Search movies by title
     */
    public function searchMovies(string $query, int $page = 1): array
    {
        try {
            $cacheKey = "search_" . md5($query . $page);

            return Cache::remember($cacheKey, now()->addHours(24), function () use ($query, $page) {
                $response = Http::get($this->baseUrl, [
                    'apikey' => $this->apiKey,
                    's' => $query,
                    'page' => $page,
                    'type' => 'movie'
                ]);

                if ($response->successful() && isset($response['Search'])) {
                    return $response->json();
                }

                throw new Exception($response['Error'] ?? 'Failed to fetch movies');
            });
        } catch (Exception $e) {
            throw new Exception('Error searching movies: ' . $e->getMessage());
        }
    }

    /**
     * Get detailed movie information by IMDB ID
     */
    public function getMovie(string $imdbId): array
    {
        try {
            $cacheKey = "movie_" . $imdbId;

            return Cache::remember($cacheKey, now()->addHours(24), function () use ($imdbId) {
                $response = Http::get($this->baseUrl, [
                    'apikey' => $this->apiKey,
                    'i' => $imdbId,
                    'plot' => 'full'
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                throw new Exception($response['Error'] ?? 'Failed to fetch movie details');
            });
        } catch (Exception $e) {
            throw new Exception('Error fetching movie: ' . $e->getMessage());
        }
    }

    /**
     * Track movie views for trending calculation
     */
    private function trackMovieView(string $imdbId): void
    {
        $key = 'movie_views_' . now()->format('Y-m-d');
        $views = Cache::get($key, []);

        if (!isset($views[$imdbId])) {
            $views[$imdbId] = 0;
        }

        $views[$imdbId]++;
        Cache::put($key, $views, now()->addDays(7));
    }

    /**
     * Get trending movies based on views
     */
    public function getTrendingMovies(int $limit = 10): array
    {
        $key = 'movie_views_' . now()->format('Y-m-d');
        $views = Cache::get($key, []);
        arsort($views);

        $trending = [];
        foreach (array_slice($views, 0, $limit, true) as $imdbId => $count) {
            try {
                $trending[] = $this->getMovie($imdbId);
            } catch (Exception $e) {
                continue;
            }
        }

        return $trending;
    }
}
