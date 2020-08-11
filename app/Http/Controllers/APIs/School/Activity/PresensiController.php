<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;

class PresensiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:guru,ketuakelas']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listPresensi(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storePresensi(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showPresensi($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updatePresensi($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyPresensi($id);
    }

    # private -> move to services
    private function listPresensi($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function storePresensi($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
    }

    private function showPresensi($id)
    {
        //
    }

    private function updatePresensi($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
    }

    private function destroyPresensi($id)
    {
        //
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'siswaid' => ['nullable', 'string', 'numeric', 'required_without:kegiatanid'],
            'kegiatanid' => ['nullable', 'string', 'numeric', 'required_without:siswaid'],
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
