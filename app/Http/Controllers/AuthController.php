<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Attempt to authenticate the user and generate a token.
     *
     * @param \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {

            Log::error("Failed to authenticate with credentials :  " . $request->validated());

            throw ValidationException::withMessages([
                'message' => ['The provided credentials are incorrect.'],
            ]);
        }

        //TODO: in a more advanced scenario, we can add ability/scope ['order-create'] to limit the token actions
        $token = $request->user()->createToken('auth-token')->plainTextToken;

        return $this->respondWithToken($token);
    }

    /**
     * Respond with the access token.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
