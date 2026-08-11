<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\FarkleScorer;
use App\Services\FarkleBot;
use App\Enums\Players;

new class extends Component
{
    public Players $activePlayer = Players::p1;
    public Players $winner = Players::p1;

    public bool $newGameFlag = true;
    public bool $startFlag = false;
    public bool $farkleFlag = false;
    public bool $winFlag = false;
    public bool $vsCPU = false;

    public int $roundTotal = 0;
    public int $rollSize = 6;

    public array $rolls = [1, 1, 1, 1, 1, 1];
    public array $hand = [];
    public array $melds = [];
    public array $cpuHand = [];

    public function roll()
    {
        $this->startFlag = true;

        if (!empty($this->hand)) {
            $newMeld = [];
            $score = $this->scoreSet($this->hand);

            foreach ($this->hand as $key => $die) {
                $newMeld[] = $die['die_value'];
            }

            $this->melds[] = [
                'meld' => $newMeld,
                'points' => $score
            ];

            $this->roundTotal = $this->scoreMelds();
            $this->rollSize -= count($this->hand);

            $this->hand = [];
        }

        $newRolls = [];

        if ($this->rollSize === 0) { $this->rollSize = 6; }

        for ($i = 0; $i < $this->rollSize; $i++) {
            $newRolls[] = rand(1, 6);
        }

        $this->rolls = $newRolls;

        $this->farkleFlag = $this->scoreRolls() === 0;
    }

    public function selectDie($index, $value)
    {
        $this->hand[] = [
            'die_index' => $index,
            'die_value' => $value
        ];

        unset($this->rolls[$index]);

        $score = $this->validateSet($this->hand) ? $this->scoreSet($this->hand) : 0;
        $this->roundTotal = $this->scoreMelds() + $score;
    }

    public function deselectDie($index)
    {
        foreach ($this->hand as $key => $die) {
            if ($die['die_index'] === $index) {
                $this->rolls[$index] = $die['die_value'];
                
                unset($this->hand[$key]);

                $this->hand = array_values($this->hand);

                break;
            }
        }

        $score = $this->validateSet($this->hand) ? $this->scoreSet($this->hand) : 0;
        $this->roundTotal = $this->scoreMelds() + $score;
    }

    public function scoreSet($diceSet) : int
    {
        $valuesOnly = array_column($diceSet, 'die_value');

        return FarkleScorer::calculateScore($valuesOnly);
    }

    public function scoreRolls() : int
    {
        return FarkleScorer::calculateScore($this->rolls);
    }

    public function validateSet($diceSet) : bool
    {
        $valuesOnly = array_column($diceSet, 'die_value');

        return FarkleScorer::validateMeld($valuesOnly);
    }

    public function scoreMelds() : int
    {
        $score = 0;

        foreach ($this->melds as $key => $meld) {
            $score += $meld['points'];
        }

        return $score;
    }

    public function endTurn()
    {
        if ($this->farkleFlag) { $this->roundTotal = 0; }

        $this->dispatch('turn-end',
            player: $this->activePlayer,
            total: $this->roundTotal
        );
    }

    #[On('next-turn')]
    public function nextTurn()
    {
        if ($this->vsCPU) {
            match($this->activePlayer) {
                Players::p1 => $this->activePlayer = Players::cpu,
                Players::cpu => $this->activePlayer = Players::p1,
                default => null,
            };
        } else {
            match($this->activePlayer) {
                Players::p1 => $this->activePlayer = Players::p2,
                Players::p2 => $this->activePlayer = Players::p1,
                default => null,
            };
        }

        $this->resetGameState();

        if ($this->activePlayer === Players::cpu && !$this->winFlag) {
            $this->cpuRollPhase();
        }
    }

    #[On('winner')]
    public function endGame($player)
    {
        $playerEnum = $player instanceof Players ? $player : Players::tryFrom($player);

        match($playerEnum) {
            Players::p1 => $this->winner = Players::p1,
            Players::p2 => $this->winner = Players::p2,
            Players::cpu => $this->winner = Players::cpu,
            default => null,
        };

        $this->newGameFlag = true;
        $this->winFlag = true;
    }

    public function resetGameState()
    {
        $this->startFlag = false;
        $this->farkleFlag = false;

        $this->roundTotal = 0;
        $this->rollSize = 6;

        $this->rolls = [1, 1, 1, 1, 1, 1];
        $this->hand = [];
        $this->melds = [];
    }

    public function newGame($vsCpuSelected)
    {
        $this->newGameFlag = false;
        $this->winFlag = false;
        $this->vsCPU = $vsCpuSelected;

        $this->resetGameState();

        $this->activePlayer = Players::p1;


        if ($vsCpuSelected) {
            $this->dispatch('new-cpu');
        } else {
            $this->dispatch('new-pvp');
        }
    }

    public function cpuRollPhase()
    {
        $this->roll();
        $this->cpuHand = FarkleBot::cpuRoll($this, $this->rolls);
        $this->dispatch('trigger-cpu-select')->self();
    }

    public function cpuSelectPhase()
    {
        if ($this->farkleFlag) {
            $this->dispatch('trigger-cpu-end')->self();
            return;
        }

        if (!empty($this->cpuHand)) {
            $index = array_pop($this->cpuHand);
            $this->selectDie($index, $this->rolls[$index]);
            $this->dispatch('trigger-cpu-select')->self();
        } else if (count($this->hand) === 6 || count($this->rolls) >= 4) {
            $this->dispatch('trigger-cpu-roll')->self();
        } else {
            $this->dispatch('trigger-cpu-end')->self();
            return;
        }
    }

    public function cpuEndPhase()
    {
        $this->endTurn();
    }
};
?>

<div
    class="game-container"
    x-data
    x-on:trigger-cpu-roll="setTimeout(() => $wire.cpuRollPhase(), 1000)"
    x-on:trigger-cpu-select="setTimeout(() => $wire.cpuSelectPhase(), 1000)"
    x-on:trigger-cpu-end="setTimeout(() => $wire.cpuEndPhase(), 1000)"
>
    <div class="roll-header">
        <h2>
            Roll
        </h2>
        <div @class([
            'active-player',
            'player-one' => $activePlayer == Players::p1,
            'player-two' => $activePlayer == Players::p2,
            'player-cpu' => $activePlayer == Players::cpu,
        ])>
            <span>{{ $activePlayer }}</span>
        </div>
    </div>
    <div @class([
        'roll-message',
        'select-message' => !$farkleFlag,
        'farkle-message' => $farkleFlag,
    ])>
        <span>
            @if (!$startFlag)
                Press start to begin
            @elseif ($farkleFlag)
                Farkle!
            @elseif (count($rolls) === 0 && $this->validateSet($hand))
                You get six new dice!
            @else
                Select your dice
            @endif
        </span>
    </div>
    <div @class([
        'dice-container',
        'active-dice' => $startFlag && !$farkleFlag,
    ])>
        @foreach ($rolls as $index => $rollValue)
            @if ($startFlag && !$farkleFlag)
                <x-dice :value="$rollValue" wire:click="selectDie({{ $index }}, {{ $rollValue }})" />
            @else
                <x-dice :value="$rollValue" />
            @endif
        @endforeach
    </div>

    <hr class="tapered">

    <h2>
        Hand
    </h2>
    <div class="hand-container">
        <div class="dice-container active-dice">
            @foreach ($hand as $selectedDie)
                <x-dice :value="$selectedDie['die_value']" wire:click="deselectDie({{ $selectedDie['die_index'] }})" />
            @endforeach
        </div>
        <div class="hand-score">
            @if ($this->validateSet($hand))
                <span>{{ $this->scoreSet($hand) }}</span> pts.
            @else
                <span>Invalid</span>
            @endif
        </div>
    </div>

    <h2>
        Melds
    </h2>
    <div class="melds-container">
        @foreach ($melds as $key => $set)
            <div class="meld-line">
                @foreach ($set['meld'] as $die)
                    <x-dice :value="$die" />  
                @endforeach
                {{ $set['points'] }}
            </div>
        @endforeach
        <div class="meld-line">
            Total: <span>{{ $this->roundTotal}}</span> pts.
        </div>
    </div>

    <hr class="tapered">

    <button
        wire:click="roll"
        class="btn roll-btn"
        @if ($activePlayer === Players::cpu || (!$this->validateSet($hand) && $startFlag))
            disabled
        @endif
    >
        @if (!$this->startFlag)
            Start
        @else
            Roll
        @endif
    </button>

    <button
        wire:click="endTurn"
        class="btn end-btn"
        @if (!$this->startFlag || (!$this->validateSet($hand) && !$farkleFlag) || $activePlayer === Players::cpu)
            disabled
        @endif
    >
        End
    </button>

    <div @class([
        'overlay-container',
        'hidden' => !$newGameFlag,
    ])>
        <div class="overlay-message">
            @if ($winFlag)
                <span class="winner-message">{{ $winner }} wins!</span>
                <span>Play again?</span>
            @else
                <span>Choose an opponent</span>
            @endif
            <div class="overlay-btn">
                <button
                    wire:click="newGame(false)"
                    class="btn btn-new-game"
                >
                    PvP
                </button>
                <button
                    wire:click="newGame(true)"
                    class="btn btn-new-game"
                >
                    CPU
                </button>
            </div>
        </div>
    </div>
</div>