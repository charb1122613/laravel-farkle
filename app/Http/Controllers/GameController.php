<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function roll()
    {
        $rolls = [
            rand(1, 6),
            rand(1, 6),
            rand(1, 6),
            rand(1, 6),
            rand(1, 6),
            rand(1, 6)
        ];

        return view('farkle', compact('rolls'));
    }
}
