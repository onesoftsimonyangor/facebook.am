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

    public function getUserById(User $user): User
    {
        return $user->load('images');
    }

    public function create(StoreUserRequest $request)
    {
        $images = $request->input('images');
        $user = auth()->user();

        $this->attachImages($user, $images);
        return $user->images;
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
        $images = $request->input('images');
        $user = auth()->user();

        if ($request->has('email') && $request->input('email') !== $user->email) {
            $existingUser = User::where('email', $request->input('email'))->first();
            if ($existingUser) {
                throw new \Exception('Email already exists in the database');
            }
        }

        try {
            DB::beginTransaction();
            $user->update($request->only(['name', 'surname', 'email', 'phone', 'birth_date']));
            $this->attachImages($user, $images);
            DB::commit();
            return $user->load('images');
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function attachImages(User $user, $images): void
    {
        $user->images()->delete();
        $user->images()->createMany($images);
    }

    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
