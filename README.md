# Movie Search Dashboard - Laravel OMDB API Integration

A Laravel 11 application that interacts with the OMDB API to search for movies, display details, and show trending movies.

## Features

- Movie Search: Search for movies by title with real-time updates
- Movie Details: View detailed information about selected movies
- Trending Movies: Track and display popular movies based on view count
- Livewire Integration: Interactive UI without full page reloads
- Exception Handling: Proper error handling for API failures
- Caching: Implementation of caching for better performance

## Prerequisites

- PHP 8.3+
- Composer
- Node.js & NPM
- Docker (for Laravel Sail)

## Installation

1. Clone the Repository:
```bash
git clone [your-repository-url]
cd movie-dashboard
```

2. Install Dependencies:
```bash
composer install
```

3. Environment Configuration:
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Add your OMDB API key to .env file
OMDB_API_KEY=your_api_key_here
OMDB_API_URL=http://www.omdbapi.com/
```

4. Start Docker Containers:
```bash
./vendor/bin/sail up -d
```

5. Install NPM Dependencies and Build Assets:
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

6. Run Migrations:
```bash
./vendor/bin/sail artisan migrate
```

## Usage

1. Search for Movies:
   - Navigate to the homepage
   - Enter a movie title in the search bar
   - Results will appear automatically
   - Click "View Details" to see more information about a movie

2. View Movie Details:
   - Click on any movie to view its detailed information
   - See ratings, plot, cast, and other movie information

3. View Trending Movies:
   - Navigate to the Trending section
   - View movies that are frequently accessed by users

## API Endpoints

- Search Movies: `GET /`
- Movie Details: `GET /movies/{imdbId}`
- Trending Movies: `GET /trending`

## Running Tests

```bash
./vendor/bin/sail artisan test
```

## Livewire Components

The application uses the following Livewire components:
- `MovieSearch`: Handles the movie search functionality
- `MovieDetails`: Displays detailed movie information
- `TrendingMovies`: Manages the trending movies display

## Technical Implementation

- Laravel 11 with Laravel Sail for containerization
- Livewire for reactive components
- Tailwind CSS for styling
- OMDB API integration with error handling
- Caching implementation for better performance
- Docker containerization for development environment

## Potential Improvements

1. Advanced Search Features:
   - Filter by year, genre, type
   - Sort results by various parameters

2. User Features:
   - User authentication
   - Favorite movies list
   - Personal watchlist

3. Performance Enhancements:
   - Implement queue for API requests
   - Advanced caching strategies

4. Additional Features:
   - Movie recommendations
   - Similar movies suggestions
   - User ratings and reviews

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
