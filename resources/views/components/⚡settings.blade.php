<?php

use App\Models\Grade;
use Livewire\Component;

new class extends Component {

    public $grades;
    public array $gradesForm = [];


    public function mount()
    {
        $this->grades = Grade::all();
        $this->gradesForm = $this->grades->pluck('percentage', 'id')->toArray();
    }

    public function save()
    {
        //validation
        $temp = 101;
        foreach ($this->gradesForm as $id => $percentage) {
            if ($id === 1 && $percentage > 100) {
                Flux::toast('Wprowadzono niepoprawny prog procentowy dla celujący');
                return;
            } elseif ($percentage >= $temp) {
                Flux::toast('Wprowadzono niepoprawne progi procentowe');
                return;
            }

            $temp = $percentage;

        }

        //save to db
        DB::transaction(function () {
            foreach ($this->gradesForm as $id => $percentage) {
                Grade::where('id', $id)->update(['percentage' => $percentage]);
            }
        });

        $this->modal('settings')->close();


    }

    public function closeModal()
    {
        $this->gradesForm = $this->grades->pluck('percentage', 'id')->toArray();
    }
};
?>

<div>
    <flux:modal @close="closeModal" name="settings" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Ustawienia</flux:heading>
                <flux:text class="mt-2">Progi procentowa na poszczególne oceny</flux:text>
            </div>
            <form wire:submit="save" class="flex flex-col gap-4">
                @foreach($grades as $g)
                    <flux:input wire:key="{{$g->id}}" label="{{$g->name}}" wire:model="gradesForm.{{$g->id}}"/>
                @endforeach
                <flux:field class="self-end mt-4">
                    <flux:button class="cursor-pointer" type="submit" variant="primary">Zapisz zmiany</flux:button>
                </flux:field>
            </form>


        </div>
    </flux:modal>
</div>
