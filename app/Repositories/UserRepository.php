<?php

namespace App\Repositories;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    protected User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function create(StoreUserRequest $request)
    {
        $images = $request->input('images');
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->attachImages($user, $images);

        return 'Success';
    }

    public function show()
    {
        $user = auth()->user();
        return $user->load('images');
    }

    public function getUserImages()
    {
        $user = auth()->user();
        return $user->images;
    }

    public function addMainImage(UserImage $userImage)
    {

        $user = auth()->user();
        $user->images()->update([
            'main_image' => false
        ]);

        $userImage->update([
            'main_image' => true
        ]);

        return 'Success';
    }

    public function update(UpdateUserRequest $request)
    {
        $user = auth()->user();

        if ($request->has('email') && $request->input('email') !== $user->email) {
            $existingUser = User::where('email', $request->input('email'))->first();
            if ($existingUser) {
                throw new \Exception('Email already exists in the database');
            }
        }
        $user->update($request->only('name', 'surname', 'email', 'phone', 'birth_date'));

        return 'Success';
    }

    public function attachImages(User $user, $images): void
    {
        $user->images()->createMany($images);
    }

    public function deleteUserImage(UserImage $userImage): ?bool
    {
        return $userImage->delete();
    }

    public function delete(): ?bool
    {
        $user = auth()->user();
        return $user->delete();
    }
}
