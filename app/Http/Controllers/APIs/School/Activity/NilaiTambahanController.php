<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;

class NilaiTambahanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:guru']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listNilaiTambahan(request());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeNilaiTambahan(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showNilaiTambahan();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateNilaiTambahan($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyNilaiTambahan();
    }

    # private -> move to services
    private function listNilaiTambahan($request)
    {
        $validator = $this->listValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getNilaiTambahan = \App\Models\School\Activity\NilaiTambahan::query();
        if (isset($request->smtid)) {
            $getNilaiTambahan = $getNilaiTambahan->where('id_semester', $request->smtid);
        } else {
            $getNilaiTambahan = $getNilaiTambahan->where('id_semester', Cur_getActiveIDSemesterNow());
        }
        if (isset($request->kegiatanid) || isset($request->siswaid)) {
            if (isset($request->kegiatanid)) {
                $getNilaiTambahan = $getNilaiTambahan->where('id_kegiatan', $request->kegiatanid);
            }
            if (isset($request->siswaid)) {
                $getNilaiTambahan = $getNilaiTambahan->where('id_siswa', $request->siswaid);
            }
            return response()->json(dataResponse($getNilaiTambahan->get()->map->nilaitambahanSimpleListMap(), '', 'Total: ' . $getNilaiTambahan->count() . ' poin kegiatan'), 200);
        }
        return response()->json(errorResponse('Tentukan [id siswa] atau [id kegiatan] yang akan dicari'), 202);
    }

    private function storeNilaiTambahan($request)
    {
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function showNilaiTambahan()
    {
        //
    }

    private function updateNilaiTambahan($id, $request)
    {
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        //
    }

    private function destroyNilaiTambahan()
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
