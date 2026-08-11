<?php

namespace App\Enums;

enum Players: string {
    case p1 = 'Player 1';
    case p2 = 'Player 2';
    case cpu = 'CPU';
}