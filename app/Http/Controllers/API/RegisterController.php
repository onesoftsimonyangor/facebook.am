<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\RegisterRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    use ApiResponse;

    private RegisterRepository $repository;

    public function __construct(RegisterRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->response201($this->repository->register($request));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->response200($this->repository->login($request));
    }
}
