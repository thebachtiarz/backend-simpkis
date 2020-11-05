<?php

namespace App\Repositories\School\Curriculum;

use App\Models\School\Curriculum\KelasGroup;

class KelasGroupRepository
{
    protected $KelasGroup;

    public function __construct()
    {
        $this->KelasGroup = new KelasGroup;
    }

    # Public
    public function findById($id)
    {
        return $this->KelasGroup->find($id);
    }

    public function create($data)
    {
        $this->KelasGroup->create($data);
    }

    # Scope
    public function searchKelasGroupByName($nama, $tingkat)
    {
        return $this->KelasGroup->where('nama_group', 'like', "%{$nama}%")
            ->where('status', Cur_setKelasStatus('active'))
            ->where('tingkat', $tingkat);
    }

    public function getAvailableGroupKelas($tingkat, $nama)
    {
        return $this->KelasGroup->where([['tingkat', $tingkat], ['nama_group', Str_pregStringOnly($nama)]]);
    }

    # Soft Delete

    # Repo

    # Private

}
