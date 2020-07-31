<?php

namespace App\Http\Controllers\APIs\Access;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Access\ForgetPassword;

class ForgetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:admin'], ['except' => ['store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(successResponse('get: ' . User_getStatusForHuman(Auth::user()->userstat->status)), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = User_getStatus(Auth::user()->userstat->status);
        if ($status == 'admin') {
            return response()->json(successResponse('admin can create for all all status'), 200);
        } elseif ($status == 'guru') {
            return response()->json(successResponse('guru only can create for student'), 200);
        }
        return _throwErrorResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Access\ForgetPassword  $forgetPassword
     * @return \Illuminate\Http\Response
     */
    public function show(ForgetPassword $forgetPassword)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Access\ForgetPassword  $forgetPassword
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ForgetPassword $forgetPassword)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Access\ForgetPassword  $forgetPassword
     * @return \Illuminate\Http\Response
     */
    public function destroy(ForgetPassword $forgetPassword)
    {
        //
    }
}
