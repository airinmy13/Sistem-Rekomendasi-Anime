<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AnimeController extends Controller
{
    /**
     * Cache path to preprocessed anime recommendations data.
     */
    protected string $dataPath;

    public function __construct()
    {
        $this->dataPath = storage_path('app/anime_data.json');
    }

    /**
     * Display the main AniMatch recommendation page.
     */
    public function index(): View
    {
        $animeList = $this->loadAnimeData();

        // Pass top 24 popular anime for initial fast rendering and marquee wall
        $topAnime = array_slice($animeList, 0, 24);

        return view('welcome', [
            'topAnime' => $topAnime,
            'totalCount' => count($animeList),
        ]);
    }

    /**
     * Search anime from the preprocessed dataset.
     */
    public function search(Request $request): JsonResponse
    {
        $query = strtolower(trim((string) $request->input('q', '')));

        if ($query === '') {
            return response()->json([]);
        }

        $animeList = $this->loadAnimeData();
        $results = [];

        foreach ($animeList as $anime) {
            $title = strtolower((string) ($anime['title'] ?? $anime['name'] ?? ''));
            $genres = implode(' ', array_map('strtolower', (array) ($anime['genres'] ?? [])));

            if (str_contains($title, $query) || str_contains($genres, $query)) {
                $results[] = $anime;
                if (count($results) >= 8) {
                    break;
                }
            }
        }

        return response()->json($results);
    }

    /**
     * Get recommendations for a specific anime ID from precomputed Cosine Similarity matrix.
     */
    public function recommendations(int $id): JsonResponse
    {
        $animeList = $this->loadAnimeData();
        $targetAnime = null;
        $animeMap = [];

        foreach ($animeList as $anime) {
            $animeMap[$anime['id']] = $anime;
            if ((int) $anime['id'] === $id) {
                $targetAnime = $anime;
            }
        }

        if (! $targetAnime) {
            return response()->json([
                'message' => 'Anime not found in local dataset',
            ], 404);
        }

        $recommendations = [];
        $rawRecs = $targetAnime['recommendations'] ?? [];

        foreach ($rawRecs as $rec) {
            $recId = $rec['id'] ?? null;
            if ($recId && isset($animeMap[$recId])) {
                $recAnime = $animeMap[$recId];
                $recAnime['matchScore'] = round((float) ($rec['match'] ?? 85));
                $recommendations[] = $recAnime;
            }
        }

        return response()->json([
            'source' => $targetAnime,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Load anime data from storage/app/anime_data.json.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function loadAnimeData(): array
    {
        if (! File::exists($this->dataPath)) {
            return [];
        }

        $content = File::get($this->dataPath);
        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }
}

