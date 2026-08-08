<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Enums\Players;

new class extends Component
{
    public int $pOneTotal = 0;
    public int $pTwoTotal = 0;

    #[On('turn-end')]
    public function handleTurnEnd($player, $total)
    {
        $playerEnum = $player instanceof Players ? $player : Players::tryFrom($player);

        match($playerEnum) {
            Players::p1 => $this->pOneTotal += $total,
            Players::p2 => $this->pTwoTotal += $total,
            default => null,
        };

        if ($this->pOneTotal >= 10000) {
            $this->dispatch('winner',
                player: Players::p1
            );
        }

        if ($this->pTwoTotal >= 10000) {
            $this->dispatch('winner',
                player: Players::p2
            );
        }
    }
    
    #[On('new-game')]
    public function newGame()
    {
        $this->pOneTotal = 0;
        $this->pTwoTotal = 0;
    }
};
?>

<div class="score-container">
    <div id="player-one">
        <span class="player">Player 1</span>
        <span class="score">{{ $pOneTotal }}</span>
    </div>
    <div id="player-two">
        <span class="player">Player 2</span>
        <span class="score">{{ $pTwoTotal }}</span>
    </div>
</div>