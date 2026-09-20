<?php

use App\Models\Grade;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;


new #[Title('Kalkulator ocen')]
class extends Component {
    public ?int $maxPoints = null;
    public $grades;
    public $grade = [];
    public $showLevels = false;
    public $amount = 0;
    public $step = 1;


    public function mount()
    {
        $this->grades = Grade::all();
        $this->gradesForm = $this->grades->pluck('percentage', 'id')->toArray();
    }

    public function updatedMaxPoints(): void
    {
        if ($this->maxPoints === null || $this->maxPoints <= 0) {
            $this->amount = 0;
            $this->grade = [];
        }
    }

    public function updatedAmount(): void
    {
        if ($this->maxPoints === null || $this->maxPoints <= 0) {
            $this->grade = [];
            return;
        }
        $result = (float)($this->amount / $this->maxPoints);
        $this->grade = match (true) {
            $result >= (float)($this->grades[0]->percentage / 100) => ['name' => 'celujący', 'color' => 'blue', 'result' => $result],
            $result >= (float)(($this->grades[1]->percentage / 100) + 7 * $this->calcUnit($this->grades[0]->percentage, $this->grades[1]->percentage)) => ['name' => 'bardzo dobry', 'color' => 'green', 'result' => $result],
            $result >= (float)(($this->grades[1]->percentage / 100) + 3 * $this->calcUnit($this->grades[0]->percentage, $this->grades[1]->percentage)) => ['name' => 'bardzo dobry', 'color' => 'blue', 'result' => $result],
            $result >= (float)($this->grades[1]->percentage / 100) => ['name' => 'bardzo dobry', 'color' => 'red', 'result' => $result],
            $result >= (float)(($this->grades[2]->percentage / 100) + 7 * $this->calcUnit($this->grades[1]->percentage, $this->grades[2]->percentage)) => ['name' => 'dobry', 'color' => 'green', 'result' => $result],
            $result >= (float)(($this->grades[2]->percentage / 100) + 3 * $this->calcUnit($this->grades[1]->percentage, $this->grades[2]->percentage)) => ['name' => 'dobry', 'color' => 'blue', 'result' => $result],
            $result >= (float)($this->grades[2]->percentage / 100) => ['name' => 'dobry', 'color' => 'red', 'result' => $result],
            $result >= (float)(($this->grades[3]->percentage / 100) + 7 * $this->calcUnit($this->grades[2]->percentage, $this->grades[3]->percentage)) => ['name' => 'dostateczny', 'color' => 'green', 'result' => $result],
            $result >= (float)(($this->grades[3]->percentage / 100) + 3 * $this->calcUnit($this->grades[2]->percentage, $this->grades[3]->percentage)) => ['name' => 'dostateczny', 'color' => 'blue', 'result' => $result],
            $result >= (float)($this->grades[3]->percentage / 100) => ['name' => 'dostateczny', 'color' => 'red', 'result' => $result],
            $result >= (float)(($this->grades[4]->percentage / 100) + 7 * $this->calcUnit($this->grades[3]->percentage, $this->grades[4]->percentage)) => ['name' => 'dopuszczający', 'color' => 'green', 'result' => $result],
            $result >= (float)(($this->grades[4]->percentage / 100) + 3 * $this->calcUnit($this->grades[3]->percentage, $this->grades[4]->percentage)) => ['name' => 'dopuszczający', 'color' => 'blue', 'result' => $result],
            $result >= (float)($this->grades[4]->percentage / 100) => ['name' => 'dopuszczający', 'color' => 'red', 'result' => $result],
            $result < $this->grades[4]->percentage / 100 => ['name' => 'niedostateczny', 'color' => 'red', 'result' => $result],
            default => []
        };
    }

    private function calcUnit($top, $bottom)
    {
        return ($top / 100 - $bottom / 100) / 10;
    }


    public function calc()
    {
        if ($this->maxPoints > 0) {
            $this->showLevels = !$this->showLevels;
        }
    }

};
?>

<section class="flex flex-col gap-4">
    <flux:card>
        <div class="flex justify-between items-end gap-2">
            <flux:field class="flex-1">
                <flux:input type="number" wire:model.live.debounce.500ms="maxPoints" label="Maksymalna ilość punktów"/>
            </flux:field>
            <flux:field>
                <flux:button type="button" variant="primary" wire:click="calc" :disabled="$maxPoints <= 0"
                             class="cursor-pointer">{{$showLevels ? 'Ukryj' : 'Pokaż'}} progi punktowe
                </flux:button>
            </flux:field>
        </div>
        <div class="flex flex-col items-center">
            <flux:radio.group wire:model.live="step" label="Zmiana wartości" variant="pills">
                <flux:radio value=0.25 label="0.25"/>
                <flux:radio value=0.5 label="0.5"/>
                <flux:radio value=1 label="1"/>
            </flux:radio.group>
        </div>
        <flux:field :disabled="$maxPoints <= 0">
            <flux:label>
                Ilość punktów
                <x-slot name="trailing">
                    <span wire:text="amount" class="tabular-nums"></span>
                </x-slot>
            </flux:label>
            <flux:slider wire:model.live="amount" min="0" :max="$maxPoints" :step="$step"/>
        </flux:field>
        <flux:text
            @class(['text-lg', 'mt-4', 'invisible' => empty($grade) || $maxPoints<=0]) :color="$grade['color'] ?? 'red'">{{round((data_get($grade, 'result') ?? 0) * 100, 1)}}
            % {{$grade['name'] ?? ''}}</flux:text>
    </flux:card>
    @if($showLevels)
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>
                        Ocena
                    </flux:table.column>
                    <flux:table.column align="center">
                        Ilość punktów
                    </flux:table.column>
                    <flux:table.column align="center">
                        Próg procentowy
                    </flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($grades as $grade)
                        <flux:table.row wire:key="{{$grade->id}}">
                            <flux:table.cell variant="strong">
                                <span
                                    class="tracking-wide text-sky-800 dark:text-sky-300">{{Str::upper($grade->name)}}</span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                {{round($grade->percentage/100 * $maxPoints * 2) / 2}}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                {{$grade->percentage}}%
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach

                </flux:table.rows>
            </flux:table>
        </flux:card>

    @endif
    <livewire:settings />
</section>
