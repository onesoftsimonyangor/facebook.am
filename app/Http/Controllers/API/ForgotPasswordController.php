<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation error'], 422);
        }

        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        DB::table('password_resets')->where('email', $email)->delete();

        $token = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new ResetPasswordMail($token));

        return response()->json(['message' => 'Password reset link sent']);
    }

    public function showResetForm($token = null)
    {
        $tokenData = DB::table('password_resets')->where('token', $token)->first();

        if (!$tokenData) {
            return response()->json(['error' => 'Invalid token'], 404);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $tokenData->email]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $validator = $request->validated();

        if (!$validator) {
            return response()->json(['error' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $token = $request->input('token');
        $email = $request->input('email');

        if (is_null($token)) {
            return response()->json(['error' => 'Token not provided'], 400);
        }

        $tokenData = DB::table('password_resets')->where('token', $token)->first();

        if (!$tokenData || $tokenData->email !== $email) {
            return response()->json(['error' => 'Invalid token'], 404);
        }

        $user = User::where('email', $tokenData->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['password' => bcrypt($request->input('password'))]);

        DB::table('password_resets')->where('email', $user->email)->delete();

        return redirect()->route('password.reset.success');
    }
}
