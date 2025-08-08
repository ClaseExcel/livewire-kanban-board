<?php

namespace ClaseExcel\LivewireKanbanBoard\Tests\Stubs;

use ClaseExcel\LivewireKanbanBoard\LivewireKanbanBoard;
use Illuminate\Support\Collection;

class SampleLivewireKanbanBoard extends LivewireKanbanBoard {
    public $recordClicked = false;
    public $statusSortedCalled = false;
    public $statusChangedCalled = false;

    public function statuses(): Collection {
        return collect([
            [
                'id' => 'todo',
                'title' => 'To Do',
            ],
            [
                'id' => 'doing',
                'title' => 'Doing',
            ],
            [
                'id' => 'done',
                'title' => 'Done',
            ],
        ]);
    }

    public function records(): Collection {
        return collect([
            [
                'id' => fake()->uuid(),
                'status' => 'todo',
                'title' => fake()->name(),
                'clicked' => false,
            ],
            [
                'id' => fake()->uuid(),
                'status' => 'completed',
                'title' => fake()->name(),
                'clicked' => false,
            ],
            [
                'id' => fake()->uuid(),
                'status' => 'completed',
                'title' => fake()->name(),
                'clicked' => false,
            ],
        ]);
    }

    public function onRecordClick($recordId) {
        $this->recordClicked = true;
    }

    public function onStatusSorted($recordId, $statusId, $orderedIds) {
        $this->statusSortedCalled = true;
    }

    public function onStatusChanged($recordId, $statusId, $fromOrderedIds, $toOrderedIds) {
        $this->statusChangedCalled = true;
    }
}
