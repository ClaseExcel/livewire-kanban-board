# Livewire Status Board

Livewire component to show records/data according to their current status on a Kanban board.

### Preview

![preview](https://github.com/ClaseExcel/livewire-kanban-board/raw/master/preview.gif)

### Installation

You can install the package via composer:

```bash
composer require claseexcel/livewire-kanban-board
```

### Requirements

This package uses `laravel/laravel 10 - 11` (https://laravel.com/) and `livewire/livewire 3` (https://livewire.laravel.com/) under the hood.

It also uses Bootstrap CSS (https://getbootstrap.com/) for base styling. 

Please make sure you include both of this dependencies before using this component. 

### Usage

In order to use this component, you must create a new Livewire component that extends from 
`LivewireKanbanBoard`

You can use `make:livewire` to create a new component. For example.
``` bash
php artisan make:livewire SalesOrdersKanbanBoard
```

In the `SalesOrdersKanbanBoard` class, instead of extending from the base Livewire `Component` class, 
extend from `LivewireKanbanBoard`. Also, remove the `render` method. 
You'll have a class similar to this snippet.
 
``` php
class SalesOrdersKanbanBoard extends LivewireKanbanBoard
{
    //
}
```

In this class, you must override the following methods to display data
```php
public function statuses() : Collection 
{
    //
}

public function swimlanes() : Collection 
{
    //
}

public function records() : Collection 
{
    //
}
```

As you may have noticed, both methods return a Collection. `statuses()` refers to all the different status values
your data may have in different points of time. `swimlanes()` refers to the different swimlane values your data may have.
`records()` on the other hand, stand for the data you want to show that could be in any of those previously defined
`statuses()` collection.

To show how these two methods work together, let's discuss an example of Sales Orders and their different status
along the sales process: Registered, Awaiting Confirmation, Confirmed, Delivered. Each Sales Order might be in a different
status at specific times. For this example, we might define the following collection for `statuses()`

```php
public function statuses() : Collection
{
    return collect([
        [
            'id' => 'registered',
            'title' => 'Registered',
        ],
        [
            'id' => 'awaiting_confirmation',
            'title' => 'Awaiting Confirmation',
        ],
        [
            'id' => 'confirmed',
            'title' => 'Confirmed',
        ],
        [
            'id' => 'delivered',
            'title' => 'Delivered',
        ],
    ]);
}
```

For each `status` we define, we must return an array with at least 2 keys: `id` and `title`.

And the following collection for `swimlanes()`

```php
public function swimlanes() : Collection
{
    return collect([
        [
            'id' => 'high',
            'title' => 'High Priority',
        ],
        [
            'id' => 'medium',
            'title' => 'Medium Priority',
        ],
        [
            'id' => 'low',
            'title' => 'Low Priority',
        ],
    ]);
}
```

For each `swimlane` we define, we must return an array with at least 2 keys: `id` and `title`.

Now, for `records()` we may define a list of Sales Orders that come from an Eloquent model in our project

```php
public function records() : Collection
{
    return SalesOrder::query()
        ->map(function (SalesOrder $salesOrder) {
            return [
                'id' => $salesOrder->id,
                'title' => $salesOrder->client,
                'status' => $salesOrder->status,
                'swimlane' => $salesOrder->swimlane,
            ];
        });
}
```

As you might see in the above snippet, we must return a collection of array items where each item must have at least
4 keys: `id`, `title` and `status` and `swimlane`. The last two are of most importance since it is going to be used
to match to which `status` the `record` belongs to. For this matter, the component matches `status`, `swimlane` and
`records` with the following comparison

```php
$status['id'] == $record['status'] && $swimlane['id'] == $record['swimlane'];
``` 

To render the component in a view, just use the Livewire tag or include syntax

```blade
<livewire:sales-orders-kanban-board />
```  

Populate the Sales Order model and you should have something similar to the following screenshot

![basic](https://github.com/ClaseExcel/livewire-kanban-board/raw/master/basic.jpg)

You can render any render and statuses of your project using this approach 👍

### Sorting and Dragging

By default, sorting and dragging between statuses is disabled. To enable it, you must include the following
props when using the view: `sortable` and `sortable-between-statuses` 

```blade
<livewire:sales-orders-kanban-board 
    :sortable="true"
    :sortable-between-statuses="true"
/>
```

`sortable` enables sorting withing each status and `sortable-between-statuses` allow drag and drop from one status 
to the other. Adding these two properties, allow you to have drag and drop in place.

You must also install the following JS dependencies in your project to enable sorting and dragging.
```bash
npm install sortablejs
```

Once installed, make them available globally in the window object. This can be done in the `bootstrap.js` file that 
ships with your Laravel app.

```javascript
window.Sortable = require('sortablejs').default;
``` 

### Behavior and Interactions

When sorting and dragging is enabled, your component can be notified when any of these events occur. The callbacks
triggered by these two events are `onStatusSorted` and `onStatusChanged`

On `onStatusSorted` you are notified about which `record` has changed position within it's `status`. You are also
given a `$orderedIds` array which holds the ids of the `records` after being sorted. You must override the following
method to get notified on this change.

```php
public function onStatusSorted($recordId, $statusId, $orderedIds)
{
    //   
}
```

On `onStatusChanged` gets triggered when a `record` is moved to another `status`. In this scenario, you get notified
about the `record` that was changed, the new `status`, the ordered ids from the previous status and the ordered ids
of the new status the record in entering. To be notified about this event, you must override the following method:

```php
public function onStatusChanged($recordId, $statusId, $fromOrderedIds, $toOrderedIds)
{
    //
}
``` 

`onStatusSorted` and `onStatusChanged` are never triggered simultaneously. You'll get notified of one or the other
when an interaction occurs. 

You can also get notified when a record in the status board is clicked via the `onRecordClick` event

```php
public function onRecordClick($recordId)
{
    //
}
``` 

To enable `onRecordClick` you must specify this behavior when rendering the component through the 
`record-click-enabled` parameter

```blade
<livewire:sales-orders-kanban-board 
    :record-click-enabled="true"
/>
```

### Styling

To modify the look and feel of the component, you can override the `styles` method and modify the base styles returned 
by this method to the view. `styles()` returns a keyed array with Tailwind CSS classes used to render each one of the components.
These base keys and styles are:

```php
return [
    'wrapper' => 'd-flex flex-nowrap overflow-x-auto rounded', // component wrapper
    'statusWrapper' => 'flex-shrink-0', // statuses wrapper
    'statusWidth' => 272, // statuses column width
    'status' => 'flex-column rounded bg-primary fw-bold mx-1 px-2', // status column wrapper 
    'statusHeader' => 'py-2 fs-5', // status header
    'statusFooter' => '', // status footer
    'statusRecords' => '', // status records wrapper 
    'record' => 'bg-white shadow rounded border fw-normal p-2 my-2', // record wrapper
    'recordContent' => '', // record content
];
```

An example of overriding the `styles()` method can be seen below

```php
public function styles()
{
    $baseStyles = parent::styles();

    $baseStyles['wrapper'] = 'd-flex flex-nowrap overflow-x-auto rounded';

    $baseStyles['statusWrapper'] = 'flex-shrink-0';

    $baseStyles['statusWidth'] = 300;

    $baseStyles['status'] = 'flex-column rounded bg-primary fw-bold mx-1 px-2';

    $baseStyles['statusHeader'] = 'py-2 fs-5';

    $baseStyles['statusRecords'] = 'overflow-y-auto';

    $baseStyles['record'] = 'bg-white shadow rounded border fw-normal p-2 my-2';

    return $baseStyles;
}
```

With these new styles, your component should look like the screenshot below

![basic](https://github.com/ClaseExcel/livewire-kanban-board/raw/master/styles.jpg)

Looks like Trello, right? 😅

### Advanced Styling and Behavior

Base views of the component can be customized as needed by exporting them to your project. To do this, run the
`php artisan vendor:publish` command and export the `livewire-kanban-board-views` tag. The command will publish
the base views under `/resources/views/vendor/livewire-kanban-board`. You can modify these base components as
needed keeping in mind to maintain the `data` attributes and `ids` along the way.

Another approach is copying the base view files into your own view files and pass them directly to your component

```blade
<livewire:sales-orders-kanban-board 
    kanban-board-view="path/to/your/kanban-board-view"
    status-view="path/to/your/status-view"
    status-header-view="path/to/your/status-header-view"
    status-footer-view="path/to/your/status-footer-view"
    record-view="path/to/your/record-view"
    record-content-view="path/to/your/record-content-view"
/>
```

Note: Using this approach also let's you add extra behavior to your component like click events on header, footers,
such as filters or any other actions

### Adding Extra Views

The component let's you add a view before and/or after the status board has been rendered. These two placeholders can
be used to add extra functionality to your component like a search input or toolbar of actions. To use them, just pass
along the views you want to use in the `before-kanban-board-view` and `after-kanban-board-view` props when displaying 
the component.

```blade
<livewire:sales-orders-kanban-board 
    before-kanban-board-view="path/to/your/before-kanban-board-view"
    after-kanban-board-view="path/to/your/after-kanban-board-view"  
/>
```

Note: These views are optional.

In the following example, a `before-kanban-board-view` has been specified to add a search text box and a button

![extra-views](https://github.com/ClaseExcel/livewire-kanban-board/raw/master/extra-views.jpg)

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email santibanez.andres@gmail.com instead of using the issue tracker.

## Credits

- [Clase de Excel](https://github.com/ClaseExcel)
- [Pieter Naber](https://github.com/mantix)
- [Mantix BV](https://mantix.nl)
- [Andrés Santibáñez](https://github.com/asantibanez)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
