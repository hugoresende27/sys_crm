<?php

namespace App\webServices;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class TheMovieDbAPI
{
    private string $token;
    private string $imageURL;
    private string $apiURL;
    private string $apiLanguage;
    private string $sortBy;
    public function __construct()
    {
        $this->token = $_ENV['MOVIE_API_TOKEN'];
        $this->imageURL = 'https://image.tmdb.org/t/p/';
        $this->apiURL = 'https://api.themoviedb.org/3';
        $this->apiLanguage = 'pt-PT';
        $this->sortBy = 'revenue.desc';
    }

    public function trendings(): array
    {
        try{
            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer '.$this->token
            ];
            $request = new Request('GET', $this->apiURL.'/trending/movie/day?language='.$this->apiLanguage, $headers);
            $response = $client->send($request);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            foreach($data['results'] as $key => $movie) {
                $data['results'][$key]['backdrop_path'] = $this->generateImageURL($this->imageURL, $movie['backdrop_path']);
                $data['results'][$key]['poster_path'] = $this->generateImageURL($this->imageURL, $movie['poster_path']);   
                $genres = $this->getGenreNames($movie['genre_ids'] );
                $data['results'][$key]['genre_ids'] = $genres;
            }

            
        } catch (Exception $e) {
            // dd($e);
            $data = (array) $e;
        }

        return $data;
     
    }

    public function getGenreNames($genreIds) : array
    {
        try {
            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $this->token,
            ];
    
            $genreIdsString = implode(',', $genreIds);
            $request = new Request('GET', $this->apiURL."/genre/movie/list?language=".
            $this->apiLanguage, $headers);
    
            $response = $client->send($request);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            $genreMap = [];
            foreach ($data['genres'] as $genre) {
                $genreMap[$genre['id']] = $genre['name'];
            }
            $genreNames = [];
            foreach ($genreIds as $genreId) {
                if (isset($genreMap[$genreId])) {
                    $genreNames[] = $genreMap[$genreId];
                }
            }
    
            return $genreNames;
        } catch (RequestException $e) {
            echo 'Error: ' . $e->getMessage();
            return (array) $e;
        }
    }

    public function generateImageURL($baseURL, $filePath, $size = 'w500')
    {
        return $baseURL . $size . $filePath;
    }

    public function popularity(): array
    {

        try{
            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer '.$this->token
            ];
            $request = new Request('GET', $this->apiURL.
            '/discover/movie?include_adult=false&include_video=false&without_genres=10402&language='
            .$this->apiLanguage.'&page=1&sort_by='.$this->sortBy, $headers);
            $response = $client->send($request);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            return $this->transformResponse($data['results']);

            
        } catch (Exception $e) {
            // dd($e);
            $data = (array) $e;
        }

        return $data;
    }
    public function byGenre(?int $genreId): array
    {
        try{
            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer '.$this->token
            ];
            $request = new Request('GET', $this->apiURL.
            '/discover/movie?with_genres='.$genreId.'&language='.$this->apiLanguage.'&page=1&sort_by='.$this->sortBy, $headers);
            $response = $client->send($request);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            return $this->transformResponse($data['results']);

            
        } catch (Exception $e) {
            // dd($e);
            $data = (array) $e;
        }

        return $data;
    }


    private function transformResponse(array $results): array
    {
        foreach ($results as $key => $movie) {
            $results[$key]['backdrop_path'] = $this->generateImageURL($this->imageURL, $movie['backdrop_path']);
            $results[$key]['poster_path'] = $this->generateImageURL($this->imageURL, $movie['poster_path']);
            $genres = $this->getGenreNames($movie['genre_ids']);
            $results[$key]['genre_ids'] = $genres;
        }

        return $results;
    }
}
