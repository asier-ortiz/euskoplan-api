<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\SendPasswordResetRequest;
use App\Http\Resources\PasswordResetResource;
use App\Http\Resources\UserResource;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Notifications\PasswordResetNotificationRequest;
use App\Notifications\PasswordResetNotificationSuccess;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


class PasswordResetController extends Controller
{

    public function sendEmail(SendPasswordResetRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user)
            return response(['message' => 'We cannot find a user with that email address'], Response::HTTP_NOT_FOUND);

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            ['email' => $user->email, 'token' => Str::random(40)]
        );

        if ($passwordReset) $user->notify(new PasswordResetNotificationRequest($passwordReset->token));
        return response(['message' => 'We have e-mailed your password reset link'], Response::HTTP_ACCEPTED);
    }

    public function find($token): \Illuminate\Http\Response|JsonResponse|Application|ResponseFactory
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset)
            return response()->json(['message' => 'This password reset token is invalid'], Response::HTTP_NOT_FOUND);

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response(['message' => 'This password reset token is invalid'], Response::HTTP_BAD_REQUEST);
        }

        return response(new PasswordResetResource($passwordReset), Response::HTTP_ACCEPTED);
    }

    public function reset(PasswordResetRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();

        $passwordReset = PasswordReset::where([
            ['token', $validatedData['token']],
            ['email', $validatedData['email']]
        ])->first();

        if (!$passwordReset) return response(['message' => 'This password reset token is invalid'], Response::HTTP_NOT_FOUND);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) return response(['message' => 'We cannot find a user with that email address'], Response::HTTP_NOT_FOUND);
        $user->password = Hash::make($validatedData['password']);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetNotificationSuccess());
        return response(new UserResource($user));
    }

}
