<?php

namespace ClaseExcel\LivewireKanbanBoard;

use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Class LivewireKanbanBoard
 * @package ClaseExcel\LivewireKanbanBoard
 * @property boolean $sortable
 * @property boolean $sortableBetweenStatuses
 * @property string $kanbanBoardView
 * @property string $swimlaneView;
 * @property string $swimlaneHeaderView;
 * @property string $swimlaneFooterView;
 * @property string $statusView
 * @property string $statusHeaderView
 * @property string $statusFooterView
 * @property string $recordView
 * @property string $recordContentView
 * @property string $sortableView
 * @property string $beforeKanbanBoardView
 * @property string $afterKanbanBoardView
 * @property string $ghostClass
 * @property boolean $recordClickEnabled
 */
class LivewireKanbanBoard extends Component {
    public $sortable;
    public $sortableBetweenStatuses;

    public $kanbanBoardView;
    public $swimlaneView;
    public $swimlaneHeaderView;
    public $swimlaneFooterView;
    public $statusView;
    public $statusHeaderView;
    public $statusFooterView;
    public $recordView;
    public $recordContentView;
    public $sortableView;
    public $beforeKanbanBoardView;
    public $afterKanbanBoardView;

    public $ghostClass;

    public $recordClickEnabled;

    public function mount(
        $sortable = false,
        $sortableBetweenStatuses = false,
        $kanbanBoardView = null,
        $swimlaneView = null,
        $swimlaneHeaderView = null,
        $swimlaneFooterView = null,
        $statusView = null,
        $statusHeaderView = null,
        $statusFooterView = null,
        $recordView = null,
        $recordContentView = null,
        $sortableView = null,
        $beforeKanbanBoardView = null,
        $afterKanbanBoardView = null,
        $ghostClass = null,
        $recordClickEnabled = false,
        $extras = []
    ) {
        $this->sortable = $sortable ?? false;
        $this->sortableBetweenStatuses = $sortableBetweenStatuses ?? false;

        $this->kanbanBoardView = $kanbanBoardView ?? 'livewire-kanban-board::kanban-board';
        $this->swimlaneView = $swimlaneView ?? 'livewire-kanban-board::swimlane';
        $this->swimlaneHeaderView = $swimlaneHeaderView ?? 'livewire-kanban-board::swimlane-header';
        $this->swimlaneFooterView = $swimlaneFooterView ?? 'livewire-kanban-board::swimlane-footer';
        $this->statusView = $statusView ?? 'livewire-kanban-board::status';
        $this->statusHeaderView = $statusHeaderView ?? 'livewire-kanban-board::status-header';
        $this->statusFooterView = $statusFooterView ?? 'livewire-kanban-board::status-footer';
        $this->recordView = $recordView ?? 'livewire-kanban-board::record';
        $this->recordContentView = $recordContentView ?? 'livewire-kanban-board::record-content';
        $this->sortableView = $sortableView ?? 'livewire-kanban-board::sortable';
        $this->beforeKanbanBoardView = $beforeKanbanBoardView ?? null;
        $this->afterKanbanBoardView = $afterKanbanBoardView ?? null;

        $this->ghostClass = $ghostClass ?? 'bg-primary-subtle';

        $this->recordClickEnabled = $recordClickEnabled ?? false;

        $this->afterMount($extras);
    }

    public function afterMount($extras = []) {
        //
    }

    public function statuses(): Collection {
        return collect();
    }

    public function swimlanes(): Collection {
        return collect();
    }

    public function records(): Collection {
        return collect();
    }

    public function isRecordInStatusAndSwimlane($record, $status, $swimlane) {
        return $record['status'] == $status['id'] && $record['swimlane'] == $swimlane['id'];
    }

    public function onStatusSorted($recordId, $statusId, $orderedIds) {
        //
    }

    public function onStatusChanged($recordId, $statusId, $fromOrderedIds, $toOrderedIds) {
        //
    }

    public function onRecordClick($recordId) {
        //
    }

    public function styles() {
        return [
            'kanbanWrapper' => '', // component wrapper
            'swimlaneWrapper' => '', // swimlanes wrapper
            'swimlane' => '', // swimlane column wrapper 
            'swimlaneHeader' => 'p-2 fs-4', // swimlane header
            'swimlaneFooter' => '', // swimlane footer
            'swimlaneRecords' => 'd-flex flex-nowrap overflow-x-auto rounded', // swimlane records wrapper 
            'statusWrapper' => 'flex-shrink-0', // statuses wrapper
            'statusWidth' => 272, // statuses column width
            'status' => 'flex-column rounded fw-bold mx-1 px-2', // status column wrapper 
            'statusHeader' => 'py-2 fs-5', // status header
            'statusFooter' => '', // status footer
            'statusRecords' => '', // status records wrapper 
            'record' => 'shadow-sm rounded border fw-normal p-2 my-2', // record wrapper
            'recordContent' => '', // record content
        ];
    }

    public function render() {
        $swimlanes = $this->swimlanes();
        $statuses = $this->statuses();
        $records = $this->records();
        $styles = $this->styles();

        $swimlanes = $swimlanes->map(function ($swimlane) use ($statuses, $records) {
            $id = $this->id ?? 'kanban';
            $swimlane['group'] = $id;
            $swimlane['statuses'] = $statuses->map(function ($status) use ($id, $swimlane, $records) {
                $status['group'] = $id;
                $status['swimlaneStatusID'] = $id . '-' . $swimlane['id'] . '-' . $status['id'];
                $status['records'] = $records->filter(function ($record) use ($id, $swimlane, $status) {
                    $record['swimlaneStatusRecordID'] = $id . '-' . $swimlane['id'] . '-' . $status['id'] . '-' . $record['id'];
                    return $this->isRecordInStatusAndSwimlane($record, $status, $swimlane);
                });
                return $status;
            });
            return $swimlane;
        });

        return view($this->kanbanBoardView)
            ->with([
                'swimlanes' => $swimlanes,
                'statuses' => $statuses,
                'records' => $records,
                'styles' => $styles,
            ]);
    }
}
