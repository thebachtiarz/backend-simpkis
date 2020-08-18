<?php

namespace App\Exports\Data;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\School\Curriculum\NilaiAkhir;

class NilaiAkhirExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return NilaiAkhir::getResultSemesterNow()->get()->map->nilaiakhirExport();
    }

    public function headings(): array
    {
        return ['#', 'NISN', 'Nama Siswa', 'Kelas', 'Semester', 'Nilai Akhir'];
    }

    /**
     * download -> set in controller or service
     * return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Data\NilaiAkhirExport, 'NilaiAkhir.xlsx');
     */
}
