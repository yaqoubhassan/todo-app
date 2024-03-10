<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

/**
 * @author Yakubu Alhassan <yaqoubdramani@gmail.com>
 */
class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $tokenObj = $user->createToken('todo-app', [], now()->addDays(365));

            return response()->json([
                'token' => $tokenObj->plainTextToken,
                'token_type' => 'Bearer',
                'token_expiration' => $tokenObj->accessToken->expires_at,
                'email_verified' => (bool) $user->email_verified_at,
                'user' => new UserResource($user)
            ]);
        } else {
            return response()->json(['error' => 'Invalid login credentials'], 401);
        }
    }
}
