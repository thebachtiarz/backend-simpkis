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
        if (isset($request->kelasid) || isset($request->siswaid)) {
            $getNilaiAkhir = \App\Models\School\Curriculum\NilaiAkhir::query();
            $getNilaiAkhir = $getNilaiAkhir->where('id_semester', isset($request->smtid) ? $request->smtid : Cur_getActiveIDSemesterNow());
            if (isset($request->kelasid)) {
                $getNilaiAkhir = $getNilaiAkhir->whereIn('id_siswa', function ($q) use ($request) {
                    $q->select('id')->from('siswas')->where('id_kelas', $request->kelasid);
                });
            }
            if (isset($request->siswaid)) $getNilaiAkhir = $getNilaiAkhir->where('id_siswa', $request->siswaid);
            return response()->json(dataResponse($getNilaiAkhir->get()->map->nilaiakhirSimpleListMap(), '', $getNilaiAkhir->count() ? $getNilaiAkhir->get()[0]->nilaiakhirgroup->catatan : []), 200);
        } elseif (isset($request->getBy)) {
            if ($request->getBy == 'smtnow') {
                $getNilaiAkhirGroup = \App\Models\School\Curriculum\NilaiAkhirGroup::where('id_semester', Cur_getActiveIDSemesterNow());
                return response()->json(dataResponse($getNilaiAkhirGroup->get()->map->NilaiAkhirGroupSimpleListMap()), 200);
            }
        }
        return response()->json(errorResponse('Tentukan [id siswa] atau [id kelas] yang akan dicari'), 202);
    }

    private function storeNilaiAkhir($request)
    {
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        $validator = $this->storeValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $semester = isset($request->smtid) ? $request->smtid : Cur_getActiveIDSemesterNow();
        $processNilaiAkhir = (new \App\Services\School\Curriculum\NilaiAkhirCreatorService($semester))->result();
        return response()->json($processNilaiAkhir, 200);
    }

    private function showNilaiAkhir($id)
    {
        $getNilaiAkhir = \App\Models\School\Curriculum\NilaiAkhir::find($id);
        if ((bool) $getNilaiAkhir) {
            return response()->json(dataResponse($getNilaiAkhir->nilaiakhirSimpleInfoMap()), 200);
        }
        return response()->json(errorResponse('Data nilai akhir tidak ditemukan'), 202);
    }

    private function updateNilaiAkhir($id, $request)
    {
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        $validator = $this->updateValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        // waitt!!, yang mau di update apanya? kan udah fix nilainya :P
    }

    private function destroyNilaiAkhir($id)
    {
        if ($this->userstat() != 'guru') return _throwErrorResponse();
        $getNilaiAkhir = \App\Models\School\Curriculum\NilaiAkhir::find($id);
        if ((bool) $getNilaiAkhir) {
            $getNilaiAkhir->delete();
            return response()->json(successResponse('Berhasil menghapus nilai akhir'), 200);
        }
        return response()->json(errorResponse('Data nilai akhir tidak ditemukan'), 202);
    }

    private function listValidator($request)
    {
        return Validator($request, [
            'kelasid' => 'nullable|numeric',
            'siswaid' => 'nullable|numeric',
            'smtid' => 'nullable|numeric',
            'getBy' => 'nullable|string|alpha'
        ]);
    }

    private function storeValidator($request)
    {
        return Validator($request, [
            'smtid' => 'nullable|numeric'
        ]);
    }

    private function updateValidator($request)
    {
        return Validator($request, []);
    }
}
