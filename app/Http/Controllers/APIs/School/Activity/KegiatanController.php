<?php

namespace App\Http\Controllers\APIs\School\Activity;

use App\Http\Controllers\Controller;

class KegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['checkrole:guru'])->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->listKegiatan();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->storeKegiatan(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showKegiatan($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->updateKegiatan($id, request());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->destroyKegiatan($id);
    }

    # private -> move to services
    protected $canAllow = ['guru' => ['7', '5'], 'ketuakelas' => ['5']];

    private function userstat() // move to constructor at services
    {
        return User_getStatus(User_checkStatus());
    }

    private function listKegiatan()
    {
        if (in_array($this->userstat(), array_keys($this->canAllow))) {
            $getKegiatan = \App\Models\School\Activity\Kegiatan::whereIn('akses', $this->canAllow[$this->userstat()]);
            if ($this->userstat() == 'ketuakelas') $getKegiatan->getAvailablePresensiNow();
            if ($getKegiatan->count()) {
                $getKegiatan = $getKegiatan->get()->map->kegiatanSimpleListMap();
                return response()->json(dataResponse($getKegiatan), 200);
            }
        }
        return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
    }

    private function storeKegiatan($request)
    {
        $validator = $this->kegiatanValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getAvailableKegiatan = \App\Models\School\Activity\Kegiatan::getAvailableKegiatan($request->nama);
        if (!$getAvailableKegiatan->count()) {
            $dataRequest = Arr_unserialize($request->nilai);
            $newNilai = [];
            foreach ($dataRequest as $key => $value) $newNilai[] = [Str_random(6) => $value];
            \App\Models\School\Activity\Kegiatan::create([
                'nama' => $request->nama,
                'nilai' => serialize(Arr_collapse($newNilai)),
                'nilai_avg' => isset($request->nilai_avg) ? $request->nilai_avg : 0,
                'hari' => Atv_setDayKegiatan($request->hari),
                'waktu_mulai' => Carbon_AnyTimeParse($request->mulai),
                'waktu_selesai' => Carbon_AnyTimeParse($request->selesai),
                'akses' => Atv_setAksesKegiatan($request->akses)
            ]);
            return response()->json(successResponse('Berhasil membuat kegiatan baru'), 201);
        }
        return response()->json(errorResponse('Jenis kegiatan [' . $request->nama . '] sudah ada'), 202);
    }

    private function showKegiatan($id)
    {
        $getKegiatan = \App\Models\School\Activity\Kegiatan::find($id);
        if (((bool) $getKegiatan) && (in_array($this->userstat(), array_keys($this->canAllow)))) return response()->json(dataResponse($getKegiatan->kegiatanSimpleInfoMap()), 200);
        return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
    }

    private function updateKegiatan($id, $request)
    {
        $validator = $this->kegiatanValidator($request->all());
        if ($validator->fails()) return response()->json(errorResponse($validator->errors()), 202);
        $getKegiatan = \App\Models\School\Activity\Kegiatan::find($id);
        if (((bool) $getKegiatan) && (in_array($this->userstat(), array_keys($this->canAllow)))) {
            $getAvailableKegiatan = \App\Models\School\Activity\Kegiatan::getAvailableKegiatan($request->nama);
            if (!$getAvailableKegiatan->count()) {
                $getReq = Arr_unserialize($request->nilai);
                $fixNilai = [];
                foreach ($getReq as $key => $value) {
                    if (is_numeric($key)) $fixNilai[] = [Str_random(6) => $value];
                    else $fixNilai[] = [$key => $value];
                }
                $getKegiatan->update([
                    'nama' => $request->nama,
                    'nilai' => serialize(Arr_collapse($fixNilai)),
                    'nilai_avg' => isset($request->nilai_avg) ? $request->nilai_avg : 0,
                    'hari' => Atv_setDayKegiatan($request->hari),
                    'waktu_mulai' => Carbon_AnyTimeParse($request->mulai),
                    'waktu_selesai' => Carbon_AnyTimeParse($request->selesai),
                    'akses' => Atv_setAksesKegiatan($request->akses)
                ]);
                return response()->json(successResponse('Berhasil memperbarui kegiatan'), 201);
            }
            return response()->json(errorResponse('Jenis kegiatan [' . $request->nama . '] sudah ada'), 202);
        }
        return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
    }

    private function destroyKegiatan($id)
    {
        $getKegiatan = \App\Models\School\Activity\Kegiatan::find($id);
        if ((bool) $getKegiatan) {
            $getKegiatan->delete();
            return response()->json(successResponse('Berhasil menghapus kegiatan'), 201);
        }
        return response()->json(errorResponse('Kegiatan tidak ditemukan'), 202);
    }

    private function kegiatanValidator($request)
    {
        return Validator($request, [
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
