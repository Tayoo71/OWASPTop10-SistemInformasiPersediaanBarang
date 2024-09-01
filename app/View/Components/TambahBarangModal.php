<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TambahBarangModal extends Component
{

    public function __construct(
        public $jenises,
        public $mereks
    ) {}

    public function render()
    {
        return view('components.tambah-barang-modal');
    }
}
