<?php

namespace App\Http\Controllers\APIs\School\Curriculum;

use App\Http\Controllers\Controller;

class NilaiAkhirController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:guru,kurikulum']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listNilaiAkhir(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeNilaiAkhir(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showNilaiAkhir($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateNilaiAkhir($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyNilaiAkhir($id);
    }

    # private -> move to services
    private function userstat() // move to constructor at services
    {
        return User_getStatus(User_checkStatus());
    }

    private function listNilaiAkhir($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function storeNilaiAkhir($request)
    {
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function showNilaiAkhir($id)
    {
        //
    }

    private function updateNilaiAkhir($id, $request)
    {
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function destroyNilaiAkhir($id)
    {
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        //
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'kelasid' => 'required|string|numeric',
            'siswaid' => 'nullable|string|numeric',
            'smtid' => 'nullable|string|numeric'
        ]);
    }

    private function storeValidator($request)
    {
        return Validator($request, []);
    }

    private function updateValidator($request)
    {
        return Validator($request, []);
    }
}
