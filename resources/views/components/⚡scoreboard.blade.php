<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Enums\Players;

new class extends Component
{
    public bool $vsCPU = false;

    public int $pOneTotal = 0;
    public int $pTwoTotal = 0;
    public int $pCpuTotal = 0;

    #[On('turn-end')]
    public function handleTurnEnd($player, $total)
    {
        $playerEnum = $player instanceof Players ? $player : Players::tryFrom($player);

        match($playerEnum) {
            Players::p1 => $this->pOneTotal += $total,
            Players::p2 => $this->pTwoTotal += $total,
            Players::cpu => $this->pCpuTotal += $total,
            default => null,
        };

        $winner = match (true) {
            $this->pOneTotal >= 10000 => Players::p1,
            $this->pTwoTotal >= 10000 => Players::p2,
            $this->pCpuTotal >= 10000 => Players::cpu,
            default => null,
        };

        if ($winner) {
            $this->dispatch('winner',
                player: $winner
            );
        } else {
            $this->dispatch('next-turn');
        }
    }
    
    #[On('new-pvp')]
    public function newPvP()
    {
        $this->vsCPU = false;
        $this->pOneTotal = 0;
        $this->pTwoTotal = 0;
        $this->pCpuTotal = 0;
    }

     #[On('new-cpu')]
    public function newCPU()
    {
        $this->vsCPU = true;
        $this->pOneTotal = 0;
        $this->pTwoTotal = 0;
        $this->pCpuTotal = 0;
    }
};
?>

<div class="score-container">
    <div class="player-one">
        <span class="player">{{ Players::p1 }}</span>
        <span class="score">{{ $pOneTotal }}</span>
    </div>
    @if ($vsCPU)
        <div class="player-cpu">
            <span class="player">{{ Players::cpu }}</span>
            <span class="score">{{ $pCpuTotal }}</span>
        </div>
    @else
        <div class="player-two">
            <span class="player">{{ Players::p2 }}</span>
            <span class="score">{{ $pTwoTotal }}</span>
        </div>
    @endif
</div>