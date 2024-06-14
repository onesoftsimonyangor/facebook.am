<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserImage;
use App\Repositories\UserRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponse;

    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
    public function show(): JsonResponse
    {
        return $this->response200($this->repository->show());
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->response201($this->repository->create($request));
    }

    public function getUserImages(): JsonResponse
    {
        return $this->response200($this->repository->getUserImages());
    }

    public function addMainImage(UserImage $userImage): JsonResponse
    {
        return $this->response201($this->repository->addMainImage($userImage));
    }

    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        return $this->response200($this->repository->update($request));
    }

    public function deleteUserImage(UserImage $userImage): JsonResponse
    {
        $this->repository->deleteUserImage($userImage);
        return $this->response204();
    }

    public function destroy(): JsonResponse
    {
        $this->repository->delete();
        return $this->response204();
    }
}
