<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class OwnerLayout extends Component
{
    public function __construct(
        public ?array $breadcrumbs = null,
        public ?string $title = null,
    ) {
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.owner');
    }
}
