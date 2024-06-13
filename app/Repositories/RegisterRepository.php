<?php

namespace App\Repositories;

use App\Exceptions\ErrorException;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterRepository
{


    protected User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->input('password'));

        return User::create($data);
    }

    public function login(LoginRequest $request): array
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            throw new UnauthorizedException('Unauthorized');
        }

        $user = Auth::user();
        $token = $user->createToken('MyTask')->accessToken;

        return ['token' => $token, 'user' => $user];
    }

    public function changePassword(ChangePasswordRequest $request): string
    {
        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            throw new ErrorException('Current password is incorrect', Response::HTTP_BAD_REQUEST);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return 'success';
    }

    public function logout(): string
    {
        $user = Auth::user();

        $user->token()->revoke();

        return 'Successfully logged out';
    }
}
