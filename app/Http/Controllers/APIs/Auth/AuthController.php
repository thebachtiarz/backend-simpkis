<?php

namespace App\Http\Controllers\APIs\Auth;

use App\Http\Controllers\Controller;
use App\Managements\Auth\AuthManagement;

class AuthController extends Controller
{
    protected $AuthMng;

    public function __construct()
    {
        $this->middleware(['auth:sanctum'], ['only' => ['credential', 'logout']]);
        $this->AuthMng = new AuthManagement;
    }

    public function login()
    {
        return $this->AuthMng->postLogin(request());
    }

    public function logout()
    {
        return $this->AuthMng->postLogout(request());
    }

    public function credential()
    {
        return $this->AuthMng->getCredential();
    }
}
