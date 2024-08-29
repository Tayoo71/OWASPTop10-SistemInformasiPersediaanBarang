<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UbahBarangModal extends Component
{
    public $barang;
    public $isEdit;
    public $jenises;
    public $mereks;

    public function __construct($barang, $jenises, $mereks)
    {
        $this->barang = $barang;
        $this->jenises = $jenises;
        $this->mereks = $mereks;
    }

    public function render()
    {
        return view('components.ubah-barang-modal');
    }
}
