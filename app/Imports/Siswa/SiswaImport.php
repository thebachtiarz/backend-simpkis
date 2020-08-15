<?php

namespace App\Imports\Siswa;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use App\Models\School\Actor\Siswa;

class SiswaImport implements ToModel, WithHeadingRow
{
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
            '0' => Rule::in(['required', 'string', 'numeric', 'digits_between:10,15']),
            '1' => Rule::in(['required', 'numeric', 'regex:/^[a-zA-Z_,.\s]+$/']),
            '2' => Rule::in(['required', 'string', 'numeric'])
        ];
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
