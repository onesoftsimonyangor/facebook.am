<?php

namespace App\Repositories;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterRepository
{
    use ApiResponse;

    protected User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $birth_date = Carbon::parse($data['birth_date']);
        $now = Carbon::now();

        if ($birth_date->diffInYears($now) < 16) {
            return response()->json(['error' => "You're still small"], 400);
        }

        $data['password'] = Hash::make($request->input('password'));

        try {
            User::create($data);
        } catch (\Exception $e) {
            return $this->response500('User creation failed');
        }

        unset($data['confirm_password']);

        return $this->response200($data);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success = $success['token'];
            return $this->response200(["Token: " . $success['token'], 'User login successfully!']);
        }
        return $this->response401('Unauthorised.');
    }

}
