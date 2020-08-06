<?php

namespace App\Http\Controllers\APIs\School\Actor;

use App\Http\Controllers\Controller;

class SiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:kurikulum'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listSiswa(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeSiswa(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showSiswa($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateSiswa($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroySiswa($id, request());
    }

    # private -> move to services
    private function listSiswa($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getSiswa = \App\Models\School\Actor\Siswa::query();
        if ($request->method == 'all') {
            $getSiswa->where('id_kelas', $request->kelasID)->withTrashed();
        } elseif ($request->method == 'deleted') {
            $getSiswa->where('id_kelas', $request->kelasID)->onlyTrashed();
        } else {
            $kelas = $request->kelasID;
            if (auth()->user()->userstat->status == User_setStatus('ketuakelas')) $kelas = auth()->user()->ketuakelas->id_kelas;
            $getSiswa->where('id_kelas', $kelas);
        }
        return response()->json(dataResponse($getSiswa->get()), 200);
    }

    private function storeSiswa($request)
    {
        //
    }

    private function showSiswa($id)
    {
        //
    }

    private function updateSiswa($id, $request)
    {
        //
    }

    private function destroySiswa($id, $request)
    {
        //
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'kelasID' => ['nullable', 'string', 'numeric', \Illuminate\Validation\Rule::requiredIf(auth()->user()->userstat->status != User_setStatus('ketuakelas'))],
            'method' => 'nullable|string|alpha'
        ], [
            'kelasID.required' => 'Kelas ID field is required.'
        ]);
    }
}
