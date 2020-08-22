<?php

namespace App\Http\Controllers\APIs\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'], ['only' => ['credential', 'logout']]);
    }

    public function login()
    {
        $validator = Validator(request()->all(), [
            'username' => 'required|string|min:3|alpha_num',
            'password' => 'required|string|min:3'
        ]);
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (Auth::attempt(request(['username', 'password']))) {
            if (User_isActive(Auth::user()->id)) return $this->respondWithToken(Auth::user()->createToken(Auth::user()->username . '-' . getClientIpAddress())->plainTextToken);
            return response()->json(errorResponse('Your account has been ' . User_getActiveStatus(Auth::user()->active) . ' due to bad behavior.'), 202);
        }
        return response()->json(errorResponse('Account not found !'), 202);
    }

    public function logout()
    {
        if (request('_action') == 'revoke') {
            if (Auth::user()->tokens()->delete()) return response()->json(successResponse('Successfully Revoke All Logins'), 201);
        } else {
            if (Auth::user()->currentAccessToken()->delete()) return response()->json(successResponse('Successfully Logout'), 201);
        }
        return response()->json(errorResponse('Failed to Logout'), 202);
    }

    public function credential()
    {
        return response()->json(dataResponse(['name' => Auth::user()->userbio->name]), 200);
    }

    # protected
    protected function respondWithToken($token)
    {
        return response()->json(dataResponse([
            'account_name' => Auth::user()->userbio->name,
            'status' => User_getStatus(Auth::user()->userstat->status),
            'access_token' => $token
        ], '', 'Authorization'));
    }
}
