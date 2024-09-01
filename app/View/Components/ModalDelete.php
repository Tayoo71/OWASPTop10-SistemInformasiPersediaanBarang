<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalDelete extends Component
{


    public function __construct(
        public $message,
        public $action
    ) {}

    public function render()
    {
        return view('components.modal-delete');
    }
}
