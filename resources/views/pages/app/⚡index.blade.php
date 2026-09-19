<?php

use App\Models\Grade;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public int $maxPoints;
    public $grades;
    public array $gradesForm = [];
    public $grade = [];
    public $showLevels = false;
    public $amount = 0;
    public $step = 1;


    public function mount()
    {
        $this->grades = Grade::all();
        $this->gradesForm = $this->grades->pluck('percentage', 'id')->toArray();
    }

    public function updatedAmount(): void
    {
        $result = (float)($this->amount / $this->maxPoints);
        $this->grade = match (true) {
            $result >= (float)($this->grades[0]->percentage / 100) => ['name' => 'celujący', 'color' => 'blue', 'result' => $result],
            $result >= (float) (($this->grades[1]->percentage / 100)  + 7 * $this->calcUnit($this->grades[0]->percentage, $this->grades[1]->percentage)) => ['name' => 'bardzo dobry', 'color' => 'gray800', 'result' => $result],
            $result >= (float) (($this->grades[1]->percentage / 100)  + 3 * $this->calcUnit($this->grades[0]->percentage, $this->grades[1]->percentage)) => ['name' => 'bardzo dobry', 'color' => 'gray500', 'result' => $result],
            $result >= (float)($this->grades[1]->percentage / 100)  => ['name' => 'bardzo dobry', 'color' => 'gray300', 'result' => $result],
            $result >= (float) (($this->grades[2]->percentage / 100)  + 7 * $this->calcUnit($this->grades[1]->percentage, $this->grades[2]->percentage)) => ['name' => 'dobry', 'color' => 'green', 'result' => $result],
            $result >= (float) (($this->grades[2]->percentage / 100)  + 3 * $this->calcUnit($this->grades[1]->percentage, $this->grades[2]->percentage)) => ['name' => 'dobry', 'color' => 'blue', 'result' => $result],
            $result >= (float)($this->grades[2]->percentage / 100) => ['name' => 'dobry', 'color' => 'red', 'result' => $result],
            $result >= (float) (($this->grades[3]->percentage / 100)  + 7 * $this->calcUnit($this->grades[2]->percentage, $this->grades[3]->percentage)) => ['name' => 'dostateczny', 'color' => 'green', 'result' => $result],
            $result >= (float) (($this->grades[3]->percentage / 100)  + 3 * $this->calcUnit($this->grades[2]->percentage, $this->grades[3]->percentage)) => ['name' => 'dostateczny', 'color' => 'blue', 'result' => $result],
            $result >= (float)($this->grades[3]->percentage / 100) => ['name' => 'dostateczny', 'color' => 'red', 'result' => $result],
            $result >= (float) (($this->grades[4]->percentage / 100)  + 7 * $this->calcUnit($this->grades[3]->percentage, $this->grades[4]->percentage)) => ['name' => 'dopuszczający', 'color' => 'green', 'result' => $result],
            $result >= (float) (($this->grades[4]->percentage / 100)  + 3 * $this->calcUnit($this->grades[3]->percentage, $this->grades[4]->percentage)) => ['name' => 'dopuszczający', 'color' => 'blue', 'result' => $result],
            $result >= (float)($this->grades[4]->percentage / 100) => ['name' => 'dopuszczający', 'color' => 'red', 'result' => $result],
            default => ['name' => 'niedostateczny', 'color' => 'red', 'result' => $result],
        };
    }

    private function calcUnit($top, $bottom) {
        return ($top/100 - $bottom/100) / 10;
    }


    public function calc()
    {
//        if ($this->maxPoints > 0) {
//            $this->showLevels = true;
//        } else {
//            $this->showLevels = false;
//        }

        if($this->maxPoints > 0) {
            $this->showLevels = !$this->showLevels;
        }
    }

    public function save()
    {
        foreach ($this->gradesForm as $id => $percentage) {
            Grade::where('id', $id)->update(['percentage' => $percentage]);
        }

        $this->modal('settings')->close();
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
                             class="cursor-pointer">Progi punktowe
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
            <flux:slider wire:model.live="amount" :max="$maxPoints" :step="$step"/>
        </flux:field>
        @if(!empty($grade))
            <flux:text class="text-lg" :color="$grade['color']">{{round($grade['result']*100, 1)}}% {{$grade['name']}}</flux:text>
        @endif
    </flux:card>
    @if($showLevels)
        <flux:card>
            <flux:heading level="1" size="lg" class="mb-2 font-semibold bg">Liczba punktów na poszczególne oceny
            </flux:heading>
            <ul class="flex flex-col gap-4 divide-y divide-gray-200">
                @foreach($grades as $grade)
                    <li class="divide-amber-900 pb-4"
                        wire:key="{{$grade->id}}">{{mb_strtoupper($grade->name)}} <br>{{round(($grade->percentage/100) * $maxPoints * 2) / 2}}</li>
                @endforeach
            </ul>
        </flux:card>

    @endif
    <flux:modal name="settings" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Ustawienia</flux:heading>
                <flux:text class="mt-2">Zmień progi procentowa na poszczególne oceny</flux:text>
            </div>
            <form wire:submit="save" class="flex flex-col gap-4">
                @foreach($grades as $g)
                    <flux:input wire:key="{{$g->id}}" label="{{$g->name}}" wire:model="gradesForm.{{$g->id}}"/>
                @endforeach
                <flux:field class="self-end mt-4">
                    <flux:button class="cursor-pointer" type="submit" variant="primary" >Save changes</flux:button>
                </flux:field>
            </form>


        </div>
    </flux:modal>
</section>
