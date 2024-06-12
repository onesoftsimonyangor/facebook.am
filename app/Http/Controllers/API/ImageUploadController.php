<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MixedImagesAndVideosRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function uploadMixedMedia(MixedImagesAndVideosRequest $request): JsonResponse
    {
        $uploadedPaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $url = $this->storeAndRetrieveUrl($image);
                $uploadedPaths[] = $url;
            }
        } else {
            return response()->json(['error' => 'No files found'], 400);
        }

        return response()->json($uploadedPaths, 200);
    }

    public function storeAndRetrieveUrl($file): array
    {
        $path = $file->store('public/images');
        $url = Storage::url($path);

        $mainType = explode('/', $file->getMimeType())[0];

        return ['path' => $url, 'file_type' => $mainType];
    }
}
