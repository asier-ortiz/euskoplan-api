<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\AccountDeletedNotificationSuccess;
use App\Notifications\PasswordUpdatedNotificationSuccess;
use Cookie;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function register(RegisterRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();

        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response(new UserResource($user), Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $validatedData = $request->validated();

        $credentials = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ];

        if (!Auth::attempt($credentials)) {
            return response(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $verified = User::where('email', '=', $credentials['email'])->first()->email_verified_at;
        if (!$verified) return response(['error' => 'Unverified email'], Response::HTTP_FORBIDDEN);
        $user = Auth::user();
        $jwt = $user->createToken('token')->plainTextToken;
        $cookie = cookie('jwt', $jwt, 60 * 24);
        return response(['jwt' => $jwt])->withCookie($cookie);
    }

    public function logout(): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $cookie = Cookie::forget('jwt');
        return response(['message' => 'Success'])->withCookie($cookie);
    }

    public function user(Request $request): UserResource
    {
        $user = $request->user();
        return new UserResource($user);
    }

    public function updatePassword(UpdatePasswordRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $user = request()->user();
        $user->update(['password' => Hash::make($request->input('password'))]);
        $user->notify(new PasswordUpdatedNotificationSuccess($user->username));
        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function destroy(): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $user = request()->user();
        $user->notify(new AccountDeletedNotificationSuccess($user->username));
        User::destroy($user->id);
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function findByEmail($email): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $user = User::where('email', '=', $email)->first();
        if (!$user) return response(['message' => 'Email available'], Response::HTTP_OK);
        return response(['error' => 'Email unavailable'], Response::HTTP_CONFLICT);
    }

    public function findByUsername($username): \Illuminate\Http\Response|Application|ResponseFactory
    {
        $user = User::where('username', '=', $username)->first();
        if (!$user) return response(['message' => 'Username available'], Response::HTTP_OK);
        return response(['error' => 'Username unavailable'], Response::HTTP_CONFLICT);
    }

}
