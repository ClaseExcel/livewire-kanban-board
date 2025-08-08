<?php

namespace ClaseExcel\LivewireKanbanBoard\Tests;

use Livewire\Features\SupportTesting\Testable;
use ClaseExcel\LivewireKanbanBoard\Tests\Stubs\SampleLivewireKanbanBoard;
use Livewire\Livewire;

class SampleLivewireKanbanBoardTest extends TestCase {
    private function createComponent($parameters = []): Testable {
        return Livewire::test(SampleLivewireKanbanBoard::class, $parameters);
    }

    /** @test */
    public function can_build_component() {
        // Arrange

        // Act
        $component = $this->createComponent();

        // Assert
        $component->assertStatus(200);

        // Check statuses
        $component->assertViewHas(
            'statuses',
            function ($statuses) {
                return count($statuses) == 3;
            }
        );

        // Check records
        $component->assertViewHas(
            'records',
            function ($records) {
                return count($records) == 3;
            }
        );
    }

    /** @test */
    public function should_call_record_click() {
        // Arrange
        $component = $this->createComponent([
            'recordClickEnabled' => true,
        ]);

        // Assert
        $component->assertViewHas(
            'recordClicked',
            function ($recordClicked) {
                return $recordClicked === false;
            }
        );

        // Act
        $component->call('onRecordClick', 'test');

        // Assert
        $component->assertViewHas(
            'recordClicked',
            function ($recordClicked) {
                return $recordClicked === true;
            }
        );
    }

    /** @test */
    public function should_trigger_onStatusSorted() {
        // Arrange
        $component = $this->createComponent();

        // Assert
        $component->assertViewHas(
            'statusSortedCalled',
            function ($statusSortedCalled) {
                return $statusSortedCalled === false;
            }
        );

        // Act
        $component->call('onStatusSorted', null, null, null);

        // Assert
        $component->assertViewHas(
            'statusSortedCalled',
            function ($statusSortedCalled) {
                return $statusSortedCalled === true;
            }
        );
    }

    /** @test */
    public function should_trigger_onStatusChanged() {
        // Arrange
        $component = $this->createComponent();

        // Assert
        $component->assertViewHas(
            'statusChangedCalled',
            function ($statusChangedCalled) {
                return $statusChangedCalled === false;
            }
        );

        // Act
        $component->call('onStatusChanged', null, null, null, null);

        // Assert
        $component->assertViewHas(
            'statusChangedCalled',
            function ($statusChangedCalled) {
                return $statusChangedCalled === true;
            }
        );
    }
}
