<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TambahBarangMasukModal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public $gudangs)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.transaksi.tambah-barang-masuk-modal');
    }
}
