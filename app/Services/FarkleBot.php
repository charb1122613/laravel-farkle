<?php

namespace App\Services;

class FarkleBot
{
    public static function cpuRoll($game, array $cpuRolls): array
    {
        $cpuHand = [];
        $targetValues = [1, 5];

        $counts = array_count_values($cpuRolls);

        $sextet = array_keys($counts, 6);
        $quintet = array_keys($counts, 5);
        $quartet = array_keys($counts, 4);
        $triplet = array_keys($counts, 3);
        $couple = array_keys($counts, 2);
        $single = array_keys($counts, 1);

        if (count($sextet) === 1) {
            $cpuHand = [0, 1, 2, 3, 4, 5];
            return $cpuHand;
        }

        if (count($quartet) === 1 && count($couple) === 1) {
            $cpuHand = [0, 1, 2, 3, 4, 5];
            return $cpuHand;
        }

        if (count($triplet) === 2) {
            $cpuHand = [0, 1, 2, 3, 4, 5];
            return $cpuHand;
        }

        if (count($couple) === 3) {
            $cpuHand = [0, 1, 2, 3, 4, 5];
            return $cpuHand;
        }

        if (count($single) === 6) {
            $cpuHand = [0, 1, 2, 3, 4, 5];
            return $cpuHand;
        }

        if (count($quintet) === 1) {
            $target = array_search(5, $counts, true);
            array_push($cpuHand, ...array_keys($cpuRolls, $target, true));
        }

        if (count($quartet) === 1) {
            $target = array_search(4, $counts, true);
            array_push($cpuHand, ...array_keys($cpuRolls, $target, true));
        }

        if (count($triplet) === 1) {
            $target = array_search(3, $counts, true);
            array_push($cpuHand, ...array_keys($cpuRolls, $target, true));
        }

        if (count($cpuHand) === 0 && $game->rolls > 3) {
            foreach ($targetValues as $target) {
                if (in_array($target, $couple, true) || in_array($target, $single, true)) {
                    $cpuHand[] = array_search($target, $cpuRolls, true);
                    break;
                }
            }
        } else {
            if (count($couple) !== 0) {
                foreach ($targetValues as $target) {
                    if (in_array($target, $couple, true)) {
                        array_push($cpuHand, ...array_keys($cpuRolls, $target, true));
                    }
                }
            }

            if (count($single) !== 0) {
                foreach ($targetValues as $target) {
                    if (in_array($target, $single, true)) {
                        array_push($cpuHand, ...array_keys($cpuRolls, $target, true));
                    }
                }
            }
        }

        return $cpuHand;
    }
}
