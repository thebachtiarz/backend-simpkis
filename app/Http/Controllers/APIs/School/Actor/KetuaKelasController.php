<?php

namespace App\Http\Controllers\APIs\School\Actor;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator;

class KetuaKelasController extends Controller
{

    public function __construct()
    {
        $this->middleware(['checkrole:guru'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listKetuaKelas(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeKetuaKelas(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showKetuaKelas($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateKetuaKelas($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyKetuaKelas($id, request());
    }

    # private -> move to services
    private function listKetuaKelas($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function storeKetuaKelas($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function showKetuaKelas($id)
    {
        //
    }

    private function updateKetuaKelas($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function destroyKetuaKelas($id, $request)
    {
        $validator = $this->softDeleteValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function listValidator($request)
    {
        return Validator($request, []);
    }

    private function storeValidator($request)
    {
        return Validator($request, []);
    }

    private function updateValidator($request)
    {
        return Validator($request, []);
    }

    private function softDeleteValidator($request)
    {
        return Validator($request, []);
    }
}
