<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalDelete extends Component
{
    public $message;
    public $action;

    public function __construct($message, $action)
    {
        $this->message = $message;
        $this->action = $action;
    }

    public function render()
    {
        return view('components.modal-delete');
    }
}
