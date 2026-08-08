<?php

namespace App\Services;

class FarkleScorer
{
    public static function calculateScore(array $diceValues): int
    {
        $score = 0;

        $counts = array_count_values($diceValues);

        $sextet = array_keys($counts, 6);
        $quintet = array_keys($counts, 5);
        $quartet = array_keys($counts, 4);
        $triplet = array_keys($counts, 3);
        $couple = array_keys($counts, 2);
        $single = array_keys($counts, 1);

        // Triplet Quartet Quintet Sextet
        // 1000    2000    4000    8000
        // 200     400     800     1600
        // 300     600     1200    2400
        // 400     800     1600    3200
        // 500     1000    2000    4000
        // 600     1200    2400    4800

        if (count($sextet) === 1) {
            $value = $sextet[0];
            $score = ($value === 1) ? 8000 : ($value * 800);
            return $score;
        }

        if (count($quartet) === 1 && count($couple) === 1) {
            return 2000;
        }

        if (count($triplet) === 2) {
            return 2500;
        }

        if (count($couple) === 3) {
            return 1250;
        }

        if (count($single) === 6) {
            return 1500;
        }

        if (count($quintet) === 1) {
            $value = $quintet[0];
            $score = ($value === 1) ? 4000 : ($value * 400);
        }

        if (count($quartet) === 1) {
            $value = $quartet[0];
            $score = ($value === 1) ? 2000 : ($value * 200);
        }

        if (count($triplet) !== 0) {
            foreach ($triplet as $value) {
                $score += ($value === 1) ? 1000 : ($value * 100);
            }
        }

        if (count($couple) !== 0) {
            foreach ($couple as $value) {
                $score += ($value === 1) ? 200 : ($value === 5 ? 100 : 0);
            }
        }

        if (count($single) !== 0) {
            foreach ($single as $value) {
                $score += ($value === 1) ? 100 : ($value === 5 ? 50 : 0);
            }
        }

        return $score;
    }

    public static function validateMeld(array $diceValues): bool
    {
        if (empty($diceValues)) { return false ;}

        $counts = array_count_values($diceValues);

        $sextet = array_keys($counts, 6);
        $quintet = array_keys($counts, 5);
        $quartet = array_keys($counts, 4);
        $triplet = array_keys($counts, 3);
        $couple = array_keys($counts, 2);
        $single = array_keys($counts, 1);

        if (count($sextet) === 1) { return true; }

        if (count($quartet) === 1 && count($couple) === 1) { return true; }

        if (count($triplet) === 2) { return true; }

        if (count($couple) === 3) { return true; }

        if (count($single) === 6) { return true; }

        if (count($couple) !== 0) {
            foreach ($couple as $value) {
                if ($value !== 1 && $value !== 5) { return false; }
            }
        }

        if (count($single) !== 0) {
            foreach ($single as $value) {
                if ($value !== 1 && $value !== 5) { return false; }
            }
        }

        return true;
    }
}
