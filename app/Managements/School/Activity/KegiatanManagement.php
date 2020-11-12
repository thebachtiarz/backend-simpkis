<?php

namespace App\Managements\School\Activity;

use App\Models\School\Activity\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KegiatanManagement
{
    protected $canAllow;

    public function __construct()
    {
        $this->canAllow = ['guru' => ['7', '5'], 'ketuakelas' => ['5']];
    }

    # Public
    public function kegiatanList($request)
    {
        if (Auth::user()->tokenCan('kegiatan:get')) {
            $validator = $this->kegiatanListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if (in_array($this->getStatus(), array_keys($this->canAllow))) {
                $getKegiatan = Kegiatan::whereInAllowToAccess($this->canAllow[$this->getStatus()]);
                if ($this->getStatus() == 'ketuakelas') $getKegiatan->getAvailablePresensiNow();
                if ($request->tipe) $getKegiatan->whereAccessType($request->tipe);
                if ($getKegiatan->count()) {
                    $getKegiatan = $getKegiatan->get()->map->kegiatanSimpleListMap();
                    return response()->json(dataResponse($getKegiatan), 200);
                }
            }
            return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function kegiatanStore($request)
    {
        if (Auth::user()->tokenCan('kegiatan:create')) {
            $validator = $this->kegiatanValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getAvailableKegiatan = Kegiatan::getAvailableKegiatan($request->nama);
            if (!$getAvailableKegiatan->count()) {
                $dataRequest = Arr_unserialize($request->nilai);
                $newNilai = [];
                foreach ($dataRequest as $key => $value) $newNilai[] = [Str_random(6) => $value];
                Kegiatan::createNewKegiatan($request, $newNilai);
                return response()->json(successResponse('Berhasil membuat kegiatan baru'), 201);
            }
            return response()->json(errorResponse('Jenis kegiatan [' . $request->nama . '] sudah ada'), 202);
        }
        return _throwErrorResponse();
    }

    public function kegiatanShow($id)
    {
        if (Auth::user()->tokenCan('kegiatan:show')) {
            $getKegiatan = Kegiatan::find($id);
            if (((bool) $getKegiatan) && (in_array($this->getStatus(), array_keys($this->canAllow))))
                return response()->json(dataResponse($getKegiatan->kegiatanSimpleInfoMap()), 200);
            return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function kegiatanUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('kegiatan:update')) {
            $validator = $this->kegiatanValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getKegiatan = Kegiatan::find($id);
            if ((bool) $getKegiatan) {
                $getReq = Arr_unserialize($request->nilai);
                $updateNilai = [];
                $currentNilai = [];
                $newNilai = [];
                foreach ($getReq as $key => $value) {
                    if (is_numeric($key)) $newNilai[] = [Str_random(6) => $value];
                    else $currentNilai[] = [$key => $value];
                }
                $updateNilai = array_merge($currentNilai, $newNilai);
                Kegiatan::updateKegiatan($id, $request, $updateNilai);
                return response()->json(successResponse('Berhasil memperbarui kegiatan'), 201);
            }
            return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function kegiatanDestory($id)
    {
        if (Auth::user()->tokenCan('kegiatan:delete')) {
            $getKegiatan = Kegiatan::find($id);
            if ((bool) $getKegiatan) {
                $getKegiatan->delete();
                return response()->json(successResponse('Berhasil menghapus kegiatan'), 201);
            }
            return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private
    private function getStatus()
    {
        return User_getStatus(Auth::user()->userstat->status);
    }

    # Validator
    private function kegiatanListValidator($request)
    {
        return Validator::make($request, [
            'tipe' => 'nullable|string|alpha'
        ]);
    }

    private function kegiatanValidator($request)
    {
        return Validator::make($request, [
            'nama' => 'required|string|regex:/^[a-zA-Z_,.\s]+$/',
            'nilai' => 'required|string',
            'nilai_avg' => 'nullable|numeric',
            'hari' => 'required|string|alpha|max:3',
            'mulai' => 'required|date_format:H:i',
            'selesai' => 'required|date_format:H:i',
            'akses' => 'required|string|alpha'
        ]);
    }
}
