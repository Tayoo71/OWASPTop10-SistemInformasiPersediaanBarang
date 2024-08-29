<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TambahBarangModal extends Component
{
    public $jenises;
    public $mereks;
    public function __construct($jenises, $mereks)
    {
        $this->jenises = $jenises;
        $this->mereks = $mereks;
    }

    public function render()
    {
        return view('components.tambah-barang-modal');
    }
}
