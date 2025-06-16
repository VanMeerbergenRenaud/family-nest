<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public string $title;

    public function __construct($title = null)
    {
        $this->title = $title ?? config('app.name', 'FamilyNest');
    }

    public function render(): View
    {
        return view('layouts.guest');
    }
}
