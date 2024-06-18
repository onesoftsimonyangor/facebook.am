<?php

namespace App\Repositories;

use App\Exceptions\ErrorException;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\VerificationEmail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            if ($existingUser->email_verified_at === null) {
                if (Carbon::now()->lessThanOrEqualTo($existingUser->verification_code_expires_at)) {
                    return response()->json(['message' => 'Verification code has not expired. Message sent to your email.'], 200);
                }

                $verificationCode = rand(100000, 999999);
                $existingUser->update([
                    'name' => $data['name'],
                    'surname' => $data['surname'],
                    'phone' => $data['phone'],
                    'birth_date' => $data['birth_date'],
                    'password' => $data['password'],
                    'verification_code' => $verificationCode,
                    'verification_code_expires_at' => Carbon::now()->addMinutes(1),
                ]);

                Mail::to($existingUser->email)->send(new VerificationEmail($verificationCode));

                return response()->json(['message' => 'User updated and verification code sent to email.'], 200);
            }

            return response()->json(['message' => 'Email already registered and verified.'], 400);
        }

        $verificationCode = rand(100000, 999999);
        $userData = [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'phone' => $data['phone'],
            'birth_date' => $data['birth_date'],
            'email' => $data['email'],
            'password' => $data['password'],
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(1),
        ];

        $user = User::create($userData);

        Mail::to($user->email)->send(new VerificationEmail($verificationCode));

        return response()->json(['message' => 'User created and verification code sent to email.'], 201);
    }

    public function verifyEmail(Request $request)
    {
        try {
            $email = $request->input('email');
            $verification_code = $request->input('verification_code');

            $user = User::where('email', $email)
                ->where('verification_code', $verification_code)
                ->first();

            if (!$user) {
                return response()->json(['message' => 'Invalid verification code'], 400);
            }

            if (Carbon::now()->greaterThan($user->verification_code_expires_at)) {
                return response()->json(['message' => 'Verification code has expired'], 400);
            }

            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->verification_code_expires_at = null;
            $user->save();

            return response()->json(['message' => 'Email verified successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }

    }

    public function login(LoginRequest $request): array
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            throw new UnauthorizedException('Unauthorized');
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            throw new UnauthorizedException('Email not verified');
        }

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
