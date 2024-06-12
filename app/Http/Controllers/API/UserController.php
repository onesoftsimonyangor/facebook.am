<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
    public function show(User $user): JsonResponse
    {
        return $this->response200($this->repository->getUserById($user));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->response201($this->repository->create($request));
    }

    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        return $this->response200($this->repository->update($request));
    }

    public function destroy(User $user): JsonResponse
    {
        $this->repository->delete($user);
        return $this->response204();
    }
}
