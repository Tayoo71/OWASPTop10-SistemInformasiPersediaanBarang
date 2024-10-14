<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PDFExport implements FromView
{
    private $headers;
    private $datas;

    public function __construct(array $headers, $datas)
    {
        $this->headers = $headers;
        $this->datas = $datas;
    }
    public function view(): View
    {
        return view('layouts.exports.export_gudang', [
            'headers' => $this->headers,
            'datas' => $this->datas
        ]);
    }
}
