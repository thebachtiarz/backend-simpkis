<?php

namespace App\Managements\Auth;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthManagement
{
    protected $UserRepo;

    public function __construct()
    {
        $this->UserRepo = new UserRepository;
    }

    # Public
    public function getCredential()
    {
        if (Auth::user()->tokenCan('auth:getCred')) {
            $_userData = [
                'name' => Auth::user()->userbio->name,
                'status' => User_getStatus(Auth::user()->userstat->status)
            ];
            return response()->json(dataResponse($_userData), 200);
        }
        return _throwErrorResponse();
    }

    public function postLogin($request)
    {
        $validator = $this->loginValidator($request);
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        if (Auth::attempt(request(['username', 'password']))) {
            if (User_isActive(Auth::user()->id)) {
                $_user = Auth::user();
                $_userIP = getClientIpAddress();
                $_tokenCan = $this->UserRepo->userCan($_user->userstat->status);
                $_tokenName = "$_user->username-$_userIP";
                $_tokenPlain = $_user->createToken($_tokenName, $_tokenCan)->plainTextToken;
                return $this->respondWithToken($_tokenPlain);
            }
            return response()->json(errorResponse('Your account has been ' . User_getActiveStatus(Auth::user()->active) . ' due to bad behavior.'), 202);
        }
        return response()->json(errorResponse('Account not found !'), 202);
    }

    public function postLogout($action)
    {
        if (Auth::user()->tokenCan('auth:postLogout')) {
            if ($action == 'revoke') {
                if (Auth::user()->tokens()->delete()) return response()->json(successResponse('Successfully Revoke All Logins'), 201);
            } else {
                if (Auth::user()->currentAccessToken()->delete()) return response()->json(successResponse('Successfully Logout'), 201);
            }
            return response()->json(errorResponse('Failed to Logout'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Protected
    protected function respondWithToken($token)
    {
        return response()->json(dataResponse([
            'account_name' => Auth::user()->userbio->name,
            'status' => User_getStatus(Auth::user()->userstat->status),
            'access_token' => $token
        ], '', 'Authorization'));
    }

    # Validator
    private function loginValidator($request)
    {
        return Validator::make($request, [
            'username' => 'required|string|min:3|alpha_num',
            'password' => 'required|string|min:3'
        ]);
    }
}
