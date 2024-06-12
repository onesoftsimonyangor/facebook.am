<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
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

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register user",
     *     description="Create a new user account.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation", "phone"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *             @OA\Property(property="phone", type="string", example="123456789"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->response201($this->repository->register($request));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->response200($this->repository->login($request));
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        return $this->response200($this->repository->changePassword($request));
    }

    public function logout(): JsonResponse
    {
        return $this->response200($this->repository->logout());
    }
}
