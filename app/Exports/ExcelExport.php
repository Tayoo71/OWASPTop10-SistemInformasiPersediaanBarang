<?php

namespace App\Exports;

use App\Models\Gudang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExcelExport implements FromCollection, WithHeadings
{
    private $headers;
    private $datas;

    public function __construct(array $headers, $datas)
    {
        $this->headers = $headers;
        $this->datas = $datas;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Mengembalikan data yang telah diberikan dari controller
        return collect($this->datas);
    }

    public function headings(): array
    {
        // Menggunakan headers yang diberikan dari controller
        return $this->headers;
    }
}