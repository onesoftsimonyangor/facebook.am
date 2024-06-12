<?php

namespace App\Repositories;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    use ApiResponse;

    protected User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function getUserById(User $user): User
    {
        return $user->load('images');
    }

    public function create(StoreUserRequest $request)
    {
        $images = $request->input('images');
        $mainImagePath = $request->input('main_image_path');
        $user = auth()->user();

        try {
            DB::beginTransaction();
            $this->attachImages($user, $images, $mainImagePath);
            DB::commit();
            return $user->load('images');
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function update(UpdateUserRequest $request)
    {
        $images = $request->input('images');
        $mainImagePath = $request->input('main_image_path');
        $user = auth()->user();

        if ($request->has('email') && $request->input('email') !== $user->email) {
            $existingUser = User::where('email', $request->input('email'))->first();
            if ($existingUser) {
                throw new \Exception('Email already exists in the database');
            }
        }

        $paths = array_column($images, 'path');
        if (!in_array($mainImagePath, $paths)) {
            return response()->json(['error' => ['message' => 'The selected main image path is invalid.']], 422);
        }

        try {
            DB::beginTransaction();
            $user->update($request->only(['name', 'surname', 'email', 'phone', 'birth_date']));
            $this->attachImages($user, $images, $mainImagePath);
            DB::commit();
            return $user->load('images');
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function attachImages(User $user, $images, $mainImagePath): void
    {
        $user->images()->delete();

        $images = array_map(function ($image) use ($user) {
            $image['user_id'] = $user->id;
            return $image;
        }, $images);

        $createdImages = $user->images()->createMany($images);

        if ($mainImagePath) {

            $user->images()->update(['main_image' => false]);

            foreach ($createdImages as $image) {
                if ($image->path === $mainImagePath) {
                    $image->main_image = true;
                    $image->save();
                    break;
                }
            }
        }
    }

    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
