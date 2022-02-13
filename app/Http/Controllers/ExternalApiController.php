<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ExternalApiController extends Controller
{
    public function getPhotos()
    {
        $rules = [
            'albumId' => ['required', 'integer'],
            'id' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'url' => ['required', 'url'],
            'thumbnailUrl' => ['required', 'url'],
        ];

        try {
            $response = Http::retry(3, 100)->get('https://jsonplaceholder.typicode.com/photos');

            if ($response->successful()) {
                $data = $response->json();

                foreach ($data as $photo) {
                    $validate = Validator::make($photo, $rules);

                    if (!$validate->fails()) {
                        Photo::firstOrCreate([
                            'album_id' => $photo['albumId'],
                            'photo_id' => $photo['id'],
                            'title' => $photo['title'],
                            'url' => $photo['url'],
                            'thumbnail_url' => $photo['thumbnailUrl']
                        ]);
                    }
                }

                $total = Photo::count();

                return response()->json([
                    'status' => 'success',
                    'message' => "Se importaron {$total} fotos"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
