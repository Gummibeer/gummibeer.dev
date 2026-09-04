<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\View\ComponentAttributeBag;

class GetTireSvgController
{
    public function __invoke(): Response
    {
        return response()->view(
            'components.svg.tire',
            ['attributes' => new ComponentAttributeBag],
            200,
            ['Content-Type' => 'image/svg+xml'],
        );
    }
}
