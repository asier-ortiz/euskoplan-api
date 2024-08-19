<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailVerificationRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Resources\EmailVerifyResource;
use App\Http\Resources\UserResource;
use App\Models\EmailVerify;
use App\Models\User;
use App\Notifications\EmailVerifyNotificationRequest;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EmailVerifyController extends Controller
{

    public function sendEmail(SendEmailVerificationRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user)
            return response(['message' => 'We cannot find a user with that email address'], Response::HTTP_NOT_FOUND);

        $emailVerify = EmailVerify::updateOrCreate(
            ['email' => $user->email],
            ['email' => $user->email, 'token' => Str::random(40)]
        );

        if ($emailVerify) $user->notify(new EmailVerifyNotificationRequest($emailVerify->token, $user->username));
        return response(['message' => 'We have e-mailed your verification link'], Response::HTTP_ACCEPTED);
    }

    public function find($token): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $emailVerify = EmailVerify::where('token', $token)->first();

        if (!$emailVerify)
            return response(['message' => 'This email verification token is invalid'], Response::HTTP_NOT_FOUND);

        if (Carbon::parse($emailVerify->updated_at)->addMinutes(720)->isPast()) {
            $emailVerify->delete();
            return response(['message' => 'This email verification token is invalid'], Response::HTTP_BAD_REQUEST);
        }

        return response(new EmailVerifyResource($emailVerify), Response::HTTP_ACCEPTED);
    }

    public function verify(VerifyEmailRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();

        $emailVerify = EmailVerify::where('token', $validatedData['token'])->first();

        if (!$emailVerify) {
            return response(['message' => 'This email verification token is invalid'], Response::HTTP_NOT_FOUND);
        }

        $user = User::where('email', $emailVerify->email)->first();

        if (!$user) {
            return response(['message' => 'We cannot find a user with that email address'], Response::HTTP_NOT_FOUND);
        }

        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();
        $emailVerify->delete();

        Auth::login($user);
        $jwt = $user->createToken('token')->plainTextToken;

        return response([
            'user' => new UserResource($user),
            'token' => $jwt
        ], Response::HTTP_OK);
    }

}
