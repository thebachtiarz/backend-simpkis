<?php

namespace App\Repositories\School\Actor;

use App\Models\School\Actor\Siswa;

class SiswaRepository
{
    protected $Siswa;

    public function __construct()
    {
        $this->Siswa = new Siswa;
    }

    # Public
    public function getByKelasId($id)
    {
        $this->Siswa->where('id_kelas', $id);
    }

    public function findById($id)
    {
        return $this->Siswa->find($id);
    }

    public function collectById($id, $key)
    {
        $siswa = $this->findById($id);
        return (bool) $siswa ? $siswa[$key] : '';
    }

    # Scope

    # Soft Delete

    # Repo

    # Private

}
