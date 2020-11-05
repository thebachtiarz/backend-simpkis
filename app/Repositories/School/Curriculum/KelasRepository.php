<?php

namespace App\Repositories\School\Curriculum;

use App\Models\School\Curriculum\Kelas;

class KelasRepository
{
    protected $Kelas;

    public function __construct()
    {
      $this->Kelas = new Kelas;
    }

    # Public
    public function findById($id)
    {
        return $this->Kelas->withTrashed()->find($id);
    }

    public function create($data)
    {
        $this->Kelas->create($data);
    }

    # Scope
    public function getAvailableKelas($tingkat, $nama)
    {
        return $this->Kelas->join('kelas_groups', 'kelas.id_group', '=', 'kelas_groups.id')
            ->where([['kelas_groups.tingkat', $tingkat], ['nama', $nama]]);
    }

    public function getKelasByID($id)
    {
        return $this->Kelas->where('id', $id);
    }

    public function getActiveKelas()
    {
        return $this->Kelas->whereIn('id_group', function ($group) {
            $group->select('id')->from('kelas_groups')->where('status', Cur_setKelasStatus('active'));
        });
    }

    public function getGraduatedKelas()
    {
        return $this->Kelas->whereIn('id_group', function ($group) {
            $group->select('id')->from('kelas_groups')->where('status', Cur_setKelasStatus('graduated'));
        });
    }

    # Soft Delete

    # Repo

    # Private

}
