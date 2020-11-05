<?php

namespace App\Managements\School\Curriculum;

use App\Repositories\School\Curriculum\KelasGroupRepository;
use App\Repositories\School\Curriculum\KelasRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KelasManagement
{
    protected $KelasRepo;
    protected $KelasGroupRepo;

    public function __construct()
    {
        $this->KelasRepo = new KelasRepository;
        $this->KelasGroupRepo = new KelasGroupRepository;
    }

    # Public
    public function kelasList($request)
    {
        if (Auth::user()->tokenCan('kelas:get')) {
            $validator = $this->kelasListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if ($request->method == 'searchgroup') {
                $getKelasGroup = $this->KelasGroupRepo->searchKelasGroupByName($request->searchnama, $request->tingkat);
                return response()->json(dataResponse($getKelasGroup->get()->map->kelasgroupSimpleListMap()), 200);
            }
            if ($request->method == 'graduated') {
                $getGraduated = $this->KelasRepo->getGraduatedKelas();
                return response()->json(dataResponse($getGraduated->get()->map->kelasSimpleListMap()), 200);
            }
            $getKelas = $this->KelasRepo->getActiveKelas();
            if ($request->method == 'havenoketuakelas') {
                $getKelas = $getKelas->whereNotIn('id', function ($q) {
                    $q->select('id_kelas')->from('ketua_kelas');
                });
            }
            if ($request->method == 'all') {
                return response()->json(dataResponse($getKelas->withTrashed()->get()->map->kelasSimpleListMap()), 200);
            } elseif ($request->method == 'deleted') {
                return response()->json(dataResponse($getKelas->onlyTrashed()->get()->map->kelasSimpleListMap()), 200);
            }
            return response()->json(dataResponse($getKelas->get()->map->kelasSimpleListMap()), 200);
        }
        return _throwErrorResponse();
    }

    public function kelasStore($request)
    {
        if (Auth::user()->tokenCan('kelas:create')) {
            $validator = $this->kelasStoreValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $checkAvailable = $this->KelasRepo->getAvailableKelas($request->tingkat, $request->nama)->count();
            if (!$checkAvailable) {
                $checkGroupKelas = $this->KelasGroupRepo->getAvailableGroupKelas($request->tingkat, $request->nama);
                if ($checkGroupKelas->count()) {
                    $getKelasGroupId = $checkGroupKelas->get()[0]->id;
                    $this->KelasRepo->create(['nama' => $request->nama, 'id_group' => $getKelasGroupId]);
                } else {
                    $newKelasGroup = $this->KelasGroupRepo->create(['tingkat' => $request->tingkat, 'nama_group' => Str_pregStringOnly($request->nama), 'status' => Cur_setKelasStatus('active')]);
                    $this->KelasRepo->create(['nama' => $request->nama, 'id_group' => $newKelasGroup->id]);
                }
                return response()->json(successResponse('Kelas berhasil dibuat'), 201);
            }
            return response()->json(errorResponse('Kelas sudah ada'), 202);
        }
        return _throwErrorResponse();
    }

    public function kelasShow($id)
    {
        if (Auth::user()->tokenCan('kelas:show')) {
            $getKelas = $this->KelasRepo->findById($id);
            return (bool) $getKelas
                ? response()->json(dataResponse($getKelas->kelasFullInfoMap()), 200)
                : response()->json(errorResponse('Kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function kelasUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('kelas:update')) {
            $validator = $this->kelasUpdateValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if (isset($request->updateTingkat)) {
                $getKelasGroup = $this->KelasGroupRepo->findById($id);
                if ((bool) $getKelasGroup) {
                    $tingkatNow = $getKelasGroup->tingkat;
                    $tingkatNew = '';
                    $message = 'Berhasil :status kelas';
                    if (($request->updateTingkat == 'naik') && ($tingkatNow < '12')) {
                        $tingkatNew = strval(intval($tingkatNow + 1));
                        $message = preg_replace_array('/:[a-z]+/', ['menaikkan'], $message);
                    } elseif (($request->updateTingkat == 'naik') && ($tingkatNow >= '12')) {
                        $tingkatNew = Cur_formatKelasLulus();
                        $getKelasGroup->update(['status' => Cur_setKelasStatus('graduated')]);
                        $message = preg_replace_array('/:[a-z]+/', ['meluluskan'], $message);
                    }
                    if (isset($tingkatNew)) {
                        $getKelasGroup->update(['tingkat' => $tingkatNew]);
                        return response()->json(successResponse($message), 201);
                    }
                }
                return response()->json(errorResponse('Group kelas tidak ditemukan'), 202);
            }
            $getKelas = $this->KelasRepo->findById($id);
            if ((bool) $getKelas) {
                $getKelas->update(['nama' => $request->nama]);
                return response()->json(successResponse('Berhasil memperbarui kelas'), 201);
            }
            return response()->json(errorResponse('Kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function kelasDestory($id, $request)
    {
        if (Auth::user()->tokenCan('kelas:delete')) {
            $validator = $this->kelasDestoryValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getKelas = $this->KelasRepo->findById($id);
            if ($getKelas->count()) {
                if ($request->method == 'force') {
                    $getKelas->forceDelete($id);
                    return response()->json(successResponse('Berhasil menghapus kelas secara permanen'), 200);
                } else {
                    $getKelas->delete($id);
                    return response()->json(successResponse('Berhasil menghapus kelas'), 200);
                }
            }
            return response()->json(errorResponse('Kelas tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private

    # Validator
    private function kelasListValidator($request)
    {
        return Validator::make($request, [
            'method' => 'nullable|string|alpha',
            'tingkat' => 'nullable|string|numeric|between:10,12',
            'searchnama' => 'nullable|string|min:3|regex:/^[a-zA-Z0-9_\s]+$/'
        ]);
    }

    private function kelasStoreValidator($request)
    {
        return Validator::make($request, [
            'tingkat' => 'required|string|numeric|between:10,12',
            'nama' => 'required|string|regex:/^[a-z0-9A-Z_\s]+$/'
        ], [
            'tingkat.between' => 'Tingkat kelas yang benar antara 10 - 12'
        ]);
    }

    private function kelasUpdateValidator($request)
    {
        return Validator::make($request, [
            'updateTingkat' => 'nullable|string|alpha',
            'nama' => 'nullable|string|regex:/^[a-z0-9A-Z_\s]+$/|required_without:updateTingkat'
        ]);
    }

    private function kelasDestoryValidator($request)
    {
        return Validator::make($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
