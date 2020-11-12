<?php

namespace App\Managements\School\Activity;

use App\Models\School\Activity\PresensiGroup;
use App\Models\School\Activity\Presensi;
use App\Models\School\Activity\Kegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PresensiManagement
{
    public function __construct()
    {
        //
    }

    # Public
    public function presensiList($request)
    {
        // return Carbon_DBdatetimeToday();
        if (Auth::user()->tokenCan('presensi:get')) {
            $validator = $this->presensiListValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            /**
             * get only today
             * mengambil history dari (presensi wajib (group))
             * yang dilakukan hari ini
             */
            if ($request->getOnly == 'today') {
                $getPresensiGroup = PresensiGroup::query();
                $message = '';
                if ($this->getStatus() == 'guru') {
                    $getPresensiGroup = $getPresensiGroup->getUnapprovedPresenceToday();
                    $message = 'Total presensi: ' . $getPresensiGroup->count() . ' belum divalidasi hari ini';
                } else {
                    $getPresensiGroup = $getPresensiGroup->getKetuaKelasPresensiToday(Auth::user()->ketuakelas->id_kelas);
                    $message = 'Total: ' . $getPresensiGroup->count() . ' presensi hari ini';
                }
                return response()->json(dataResponse($getPresensiGroup->get()->map->presensigroupSimpleListMap(), '', $message), 200);
            }
            /**
             * get where day
             * mengambil history dari (presensi wajib (group))
             * berdasarkan hari
             */
            if ($request->getOnly == 'wheredate') {
                $getPresensiGroup = PresensiGroup::query();
                $message = '';
                if ($this->getStatus() == 'guru') {
                    $getPresensiGroup = $getPresensiGroup->getUnapprovedPresenceByDate($request->date);
                    $message = 'Total: ' . $getPresensiGroup->count() . ' presensi dilakukan';
                } else {
                    $getPresensiGroup = $getPresensiGroup->getKetuaKelasPresensiByDate(Auth::user()->ketuakelas->id_kelas, $request->date);
                    $message = 'Total: ' . $getPresensiGroup->count() . ' presensi dilakukan';
                }
                return response()->json(dataResponse($getPresensiGroup->get()->map->presensigroupSimpleListMap(), '', $message), 200);
            }
            /**
             * get presensi by presensi group id
             * mengambil data (presensi wajib) yang dilakukan
             * berdasarkan id presensi group
             */
            if (isset($request->presensiid)) {
                $getPresensi = PresensiGroup::find($request->presensiid);
                if ((bool) $getPresensi) {
                    return response()->json(dataResponse($getPresensi->presensi->map->presensiDetailListMap(), '', [
                        'info' => 'Presensi: ' . $getPresensi->kegiatan->nama . ', Kelas: ' . Cur_getKelasNameByID($getPresensi->presensi[0]->siswa->id_kelas),
                        'kegiatanid' => strval($getPresensi->kegiatan->id),
                        'catatan' => $getPresensi->catatan
                    ]), 200);
                }
                return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
            }
            /**
             * get presensi by kelas id OR siswa id
             * mengambil data (presensi wajib) yang dilakukan
             * berdasarkan semester ini atau pilihan
             * berdasarkan id kegiatan presensi wajib
             * berdasarkan id kelas atau id siswa atau keduanya
             */
            if (isset($request->kelasid) || isset($request->siswaid)) {
                $getPresensi = Presensi::where('id_semester', isset($request->smtid) ? $request->smtid : Cur_getActiveIDSemesterNow());
                if (isset($request->kegiatanid)) $getPresensi = $getPresensi->getByKegiatanId($request->kegiatanid);
                if (isset($request->kelasid)) $getPresensi = $getPresensi->getByKelasId($request->kelasid);
                if (isset($request->siswaid)) $getPresensi = $getPresensi->getBySiswaId($request->siswaid);
                return response()->json(dataResponse($getPresensi->get()->map->presensiSimpleListMap(), '', 'Total: ' . $getPresensi->count() . ' rekap presensi'), 200);
            }
            return response()->json(errorResponse('Tentukan [id siswa] atau [id kelas] yang akan dicari'), 202);
        }
        return _throwErrorResponse();
    }

    public function presensiStore($request)
    {
        if (Auth::user()->tokenCan('presensi:create')) {
            $validator = $this->presensiStoreValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            $getKegiatan = Kegiatan::getKegiatanPresensi()->find($request->kegiatanid);
            if ((bool) $getKegiatan) {
                /**
                 * cek status yang melakukan presensi apakah ketuakelas
                 * jika iya, maka akan dilakukan pemeriksaan waktu dan status approve 5
                 * jika bukan, status approve 7
                 */
                if ($this->getStatus() == 'ketuakelas') {
                    if (!Atv_boolPresensiTimeAllowed($getKegiatan->hari, $getKegiatan->waktu_mulai, $getKegiatan->waktu_selesai)) return response()->json(errorResponse('Presensi tidak dilakukan pada waktunya'), 202);
                    $approve = '5';
                } else $approve = '7';
                $getKegiatan = $getKegiatan->kegiatanCollectMap();
                $kodeKegiatan = Arr_pluck($getKegiatan['nilai'], 'code');
                $getDataPresensi = (new Presensi)->getNilai($request->presensidata); // unserialize
                $newPresensi = [];
                $newPresensiGroup = PresensiGroup::create(['id_kegiatan' => $request->kegiatanid, 'id_user' => Auth::user()->id, 'catatan' => $request->catatan, 'approve' => $approve]);
                /**
                 * melakukan penyaringan (filter)
                 * apakah poin nilai yang dilakukan pada presensi
                 * terdapat pada poin nilai kegiatan
                 */
                foreach ($getDataPresensi as $key => $value)
                    if (in_array($value['nilai'], $kodeKegiatan)) $newPresensi[] = ['id_presensi' => strval($newPresensiGroup->id), 'id_semester' => strval(Cur_getActiveIDSemesterNow()), 'id_siswa' => $value['id_siswa'], 'nilai' => $value['nilai']];
                if (count($newPresensi)) Presensi::insert($newPresensi);
                return response()->json(successResponse('Berhasil melakukan presensi'), 201);
            }
            return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function presensiShow($id)
    {
        if (Auth::user()->tokenCan('presensi:show')) {
            $getPresensi = Presensi::find($id);
            if ((bool) $getPresensi) return response()->json(dataResponse($getPresensi->presensiSimpleInfoMap()), 200);
            return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    public function presensiUpdate($id, $request)
    {
        if (Auth::user()->tokenCan('presensi:update')) {
            $validator = $this->presensiUpdateValidator($request->all());
            if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
            if (isset($request->_update)) {
                /**
                 * proses guru melakukan approve pada presensi wajib (group)
                 * berdasarkan id presensi (group)
                 */
                if ($request->_update == 'approve') {
                    $getPresensiGroup = PresensiGroup::find($id);
                    if ((bool) $getPresensiGroup) {
                        $getPresensiGroup->update(['approve' => '7']);
                        return response()->json(successResponse('Berhasil menyetujui presensi'), 201);
                    }
                    return response()->json(errorResponse('Kegiatan presensi tidak ditemukan'), 202);
                }
            } else {
                /**
                 * melakukan update perubahan nilai poin pada presensi wajib
                 * berdasarkan id presensi
                 */
                $getPresensi = Presensi::find($id);
                $getKegiatan = Kegiatan::find((bool) $getPresensi ? $getPresensi->id_kegiatan : '');
                if (((bool) $getPresensi) && ((bool) $getKegiatan)) {
                    $getNilaiData = Arr_unserialize($getKegiatan->nilai);
                    $getKegiatanKey = in_array($request->nilai, array_keys($getNilaiData));
                    if ((bool) $getKegiatanKey) {
                        $result = ['siswa' => $getPresensi->siswa->nama, 'kegiatan' => $getKegiatan->nama, 'poin_lama' => $getNilaiData[$getPresensi->nilai]['name'], 'poin_baru' => $getNilaiData[$request->nilai]['name']];
                        $getPresensi->update(['nilai' => $request->nilai]);
                        return response()->json(successResponse('Berhasil memperbarui data presensi', $result), 201);
                    }
                    return response()->json(errorResponse('Poin kegiatan tidak ditemukan'), 202);
                }
                return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
            }
        }
        return _throwErrorResponse();
    }

    public function presensiDestory($id)
    {
        if (Auth::user()->tokenCan('presensi:delete')) {
            $getPresensi = Presensi::find($id);
            if ((bool) $getPresensi) {
                $getPresensi->delete();
                return response()->json(successResponse('Berhasil menghapus presensi'), 200);
            }
            return response()->json(errorResponse('Presensi tidak ditemukan'), 202);
        }
        return _throwErrorResponse();
    }

    # Private
    private function getStatus()
    {
        return User_getStatus(Auth::user()->userstat->status);
    }

    # Validator
    private function presensiListValidator($request)
    {
        return Validator::make($request, [
            'getOnly' => 'nullable|string|alpha',
            'date' => 'nullable|date_format:Y-m-d|required_if:getOnly,wheredate',
            'presensiid' => 'nullable|string|numeric|required_without_all:getOnly,siswaid,kelasid',
            'siswaid' => ['nullable', 'string', 'numeric', Rule::requiredIf(isset($request['kelasid']))],
            'kelasid' => 'nullable|string|numeric',
            'kegiatanid' => 'nullable|string|numeric',
            'smtid' => 'nullable|string|numeric'
        ]);
    }

    private function presensiStoreValidator($request)
    {
        return Validator::make($request, [
            'catatan' => 'nullable|string|regex:/^[a-zA-Z0-9_,.\s]+$/',
            'kegiatanid' => 'required|string|numeric',
            'presensidata' => 'required|string'
        ]);
    }

    private function presensiUpdateValidator($request)
    {
        return Validator::make($request, [
            'nilai' => 'nullable|string|alpha_num|size:6|required_without:_update',
            '_update' => 'nullable|string|alpha'
        ]);
    }
}
