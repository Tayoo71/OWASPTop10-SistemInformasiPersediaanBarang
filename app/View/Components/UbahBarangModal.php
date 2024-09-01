<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UbahBarangModal extends Component
{


    public function __construct(
        public $barang,
        public $jenises,
        public $mereks,
    ) {}

    public function render()
    {
        return view('components.master_data.ubah-barang-modal');
    }
}
