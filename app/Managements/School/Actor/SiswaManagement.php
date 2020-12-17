<?php

namespace App\Managements\School\Actor;

use App\Imports\Siswa\SiswaImport;
use App\Models\School\Actor\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class SiswaManagement
{
    # Public
    public function siswaList($request)
    {
        if (Auth::user()->tokenCan('siswa:get')) {
            $validator = $this->siswaListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getSiswa = Siswa::query();
            if ($request->method == 'all') $getSiswa->getByKelasId($request->kelasid)->withTrashed();
            elseif ($request->method == 'deleted') $getSiswa->getByKelasId($request->kelasid)->onlyTrashed();
            else {
                $id_kelas = $request->kelasid;
                if ($this->getStatus() == 'ketuakelas') $id_kelas = Auth::user()->ketuakelas->id_kelas;
                if (isset($request->presensikegiatan)) $getSiswa->getUnPresensiByKegiatanToday($request->presensikegiatan);
                if (isset($request->searchname)) $getSiswa->searchSiswaByName($request->searchname);
                if (isset($id_kelas)) $getSiswa->getByKelasId($id_kelas);
            }
            return response()->json(dataResponse($getSiswa->get()->map->siswaSimpleListMap()), 200);
        }
        return _throwErrorResponse();
    }

    public function siswaStore($request)
    {
        if (Auth::user()->tokenCan('siswa:create')) {
            $validator = $this->siswaStoreValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            try {
                $catchSiswa = Arr_collapse(Excel::toArray(new SiswaImport, $request->file('file')));
                if (!count($catchSiswa)) return response()->json(errorResponse('Data siswa tidak ada!'), 202);
                $findDuplicate = Siswa::select(['nisn', 'nama', 'id_kelas'])->whereIn('nisn', array_column($catchSiswa, 'nisn'))->get();
                if (count($findDuplicate)) return response()->json(dataResponse($findDuplicate, 'error', 'Terdapat duplikasi data: ' . count($findDuplicate) . ' siswa'), 202);
                try {
                    Excel::import(new SiswaImport, $request->file('file'));
                    return response()->json(successResponse('Berhasil menambahkan data siswa'), 200);
                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                    $failures = $e->failures();
                    $fail = [];
                    foreach ($failures as $failure) {
                        $row = $failure->row();
                        $col = $failure->attribute();
                        $fail[] = '[baris:' . $row . ', kolom:' . $col . ']';
                    }
                    return response()->json(errorResponse('Terdapat kesalahan pada input data: ' . join(", ", $fail)), 202);
                }
            } catch (\Throwable $th) {
                return response()->json(errorResponse('Format file bermasalah, harap periksa sesuai dengan ketentuan yang telah disediakan'), 202);
            }
        }
        return _throwErrorResponse();
    }

    public function siswaShow($id)
    {
        if (Auth::user()->tokenCan('siswa:show')) {
            $getSiswa = Siswa::withTrashed()->find($id);
            if ((bool) $getSiswa) {
                /**
                 * jika saya == ketuakelas && siswa yang saya cari memiliki kelas yang sama dengan saya
                 * atau
                 * saya != ketuakelas
                 * maka benar
                 */
                if ((($this->getStatus() == 'ketuakelas') && ($getSiswa->kelasid == Auth::user()->ketuakelas->kelasid)) || ($this->getStatus() != 'ketuakelas'))
                    return response()->json(dataResponse($getSiswa->siswaSimpleInfoMap()), 200);
            }
            return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function siswaUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('siswa:update')) {
            $validator = $this->siswaUpdateValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getChanges = $validator->validated();
            if ((bool) $getChanges) {
                $getSiswa = Siswa::find($id);
                if ((bool) $getSiswa) {
                    if (array_key_exists('kelasid', $getChanges)) {
                        $getChanges['id_kelas'] = $getChanges['kelasid'];
                        unset($getChanges['kelasid']);
                    }
                    try {
                        $getSiswa->update($getChanges);
                        if ((bool) $getSiswa->ketuakelas && isset($request->nama)) $getSiswa->ketuakelas->user->userbio->update(['name' => $request->nama]);
                        return response()->json(successResponse('Berhasil memperbarui data siswa'), 200);
                    } catch (\Throwable $th) {
                        return response()->json(dataResponse(['code' => $th->getCode()], 'error', 'Gagal memperbarui data siswa'), 202);
                    }
                }
                return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
            }
            return response()->json(errorResponse('Silahkan sebutkan apa yang ingin diubah'), 202);
        }
        return _throwErrorResponse();
    }

    public function siswaDestory($id, $request)
    {
        if (Auth::user()->tokenCan('siswa:delete')) {
            $validator = $this->siswaDestoryValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getSiswa = Siswa::where('id', $id);
            if ($request->method == 'force') $getSiswa->withTrashed();
            if ($getSiswa->count()) {
                if ($request->method == 'force') {
                    $getSiswa->forceDelete();
                    return response()->json(successResponse('Berhasil menghapus siswa secara permanen'), 200);
                } else {
                    $getSiswa->delete();
                    return response()->json(successResponse('Berhasil menghapus siswa'), 200);
                }
            }
            return response()->json(errorResponse('Siswa tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private
    private function getStatus()
    {
        return User_getStatus(Auth::user()->userstat->status);
    }

    # Validator
    private function siswaListValidator($request)
    {
        return Validator::make($request, [
            'kelasid' => ['nullable', 'numeric', Rule::requiredIf(($this->getStatus() != 'ketuakelas') && (!isset($request['searchname'])))],
            'presensikegiatan' => 'nullable|numeric',
            'searchname' => 'nullable|string|min:3|regex:/^[a-zA-Z_,.\s]+$/',
            'method' => 'nullable|string|alpha'
        ], [
            'kelasid.required' => 'Kelas ID field is required.'
        ]);
    }

    private function siswaStoreValidator($request)
    {
        return Validator::make($request, [
            'file' => 'required|file'
        ]);
    }

    private function siswaUpdateValidator($request)
    {
        return Validator::make($request, [
            'nisn' => 'nullable|numeric|digits_between:10,15',
            'nama' => 'nullable|string|regex:/^[a-zA-Z_,.\s]+$/',
            'kelasid' => 'nullable|numeric'
        ]);
    }

    private function siswaDestoryValidator($request)
    {
        return Validator::make($request, [
            'method' => 'nullable|string|alpha'
        ]);
    }
}
