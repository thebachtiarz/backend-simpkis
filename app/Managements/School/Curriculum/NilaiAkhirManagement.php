<?php

namespace App\Managements\School\Curriculum;

use App\Models\School\Curriculum\NilaiAkhirGroup;
use App\Models\School\Curriculum\NilaiAkhir;
use App\Services\School\Curriculum\NilaiAkhirCreatorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NilaiAkhirManagement
{
    # Public
    public function nilaiAkhirList($request)
    {
        if (Auth::user()->tokenCan('nilakh:get')) {
            $validator = $this->nilaiAkhirListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            /**
             * melihat data nilai akhir
             * berdasarkan id kelas / id siswa / id nilai akhir (group)
             */
            if (isset($request->kelasid) || isset($request->siswaid) || isset($request->groupid)) {
                $getNilaiAkhir = NilaiAkhir::where('id_semester', isset($request->smtid) ? $request->smtid : Cur_getActiveIDSemesterNow());
                if (isset($request->kelasid)) $getNilaiAkhir = $getNilaiAkhir->getByKelasId($request->kelasid);
                if (isset($request->siswaid)) $getNilaiAkhir = $getNilaiAkhir->getBySiswaId($request->siswaid);
                if (isset($request->groupid)) $getNilaiAkhir = $getNilaiAkhir->getByGroupId($request->groupid);
                return response()->json(dataResponse($getNilaiAkhir->get()->map->nilaiakhirSimpleListMap(), '', $getNilaiAkhir->count() ? $getNilaiAkhir->get()[0]->nilaiakhirgroup->catatan : []), 200);
            } elseif (isset($request->getBy)) {
                /**
                 * melihat list nilai akhir dari nilai akhir (group)
                 * berdasarkan semester sekarang
                 */
                if ($request->getBy == 'smtnow') {
                    $getNilaiAkhirGroup = NilaiAkhirGroup::getBySemesterNow();
                    return response()->json(dataResponse($getNilaiAkhirGroup->get()->map->NilaiAkhirGroupSimpleListMap()), 200);
                }
            }
            return response()->json(errorResponse('Tentukan [id siswa] atau [id kelas] yang akan dicari'), 202);
        }
        return _throwErrorResponse();
    }

    public function nilaiAkhirStore($request)
    {
        if (Auth::user()->tokenCan('nilakh:create')) {
            $validator = $this->nilaiAkhirStoreValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            /**
             * proses melakukan perhitungan nilai akhir
             */
            $processNilaiAkhir = NilaiAkhirCreatorService::runProcessNilaiAkhir();
            return response()->json($processNilaiAkhir, 200);
        }
        return _throwErrorResponse();
    }

    public function nilaiAkhirShow($id)
    {
        if (Auth::user()->tokenCan('nilakh:show')) {
            $getNilaiAkhir = NilaiAkhir::find($id);
            if ((bool) $getNilaiAkhir) {
                return response()->json(dataResponse($getNilaiAkhir->nilaiakhirSimpleInfoMap()), 200);
            }
            return response()->json(errorResponse('Data nilai akhir tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function nilaiAkhirUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('nilakh:update')) {
            return response()->json(successResponse('waitt!!, yang mau di update apanya? kan udah fix nilainya :P'), 200);
        }
        return _throwErrorResponse();
    }

    public function nilaiAkhirDestory($id)
    {
        if (Auth::user()->tokenCan('nilakh:delete')) {
            $getNilaiAkhir = NilaiAkhir::find($id);
            if ((bool) $getNilaiAkhir) {
                $getNilaiAkhir->delete();
                return response()->json(successResponse('Berhasil menghapus nilai akhir'), 200);
            }
            return response()->json(errorResponse('Data nilai akhir tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Validator
    private function nilaiAkhirListValidator($request)
    {
        return Validator::make($request, [
            'kelasid' => 'nullable|numeric',
            'siswaid' => 'nullable|numeric',
            'groupid' => 'nullable|numeric',
            'smtid' => 'nullable|numeric',
            'getBy' => 'nullable|string|alpha'
        ]);
    }

    private function nilaiAkhirStoreValidator($request)
    {
        return Validator::make($request, [
            'smtid' => 'nullable|numeric'
        ]);
    }
}
