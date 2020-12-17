<?php

namespace App\Imports\Siswa;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\School\Actor\Siswa;
use Maatwebsite\Excel\Concerns\Importable;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Siswa([
            'nisn' => $row['nisn'],
            'nama' => $row['nama'],
            'id_kelas' => $row['id_kelas']
        ]);
    }

    public function rules(): array
    {
        return [
            'nisn' => 'required|numeric|digits_between:10,15',
            'nama' => 'required|string|regex:/^[a-zA-Z_,.\s]+$/',
            'id_kelas' => 'required|numeric'
        ];
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
