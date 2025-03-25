<?php

namespace App\Http\Livewire;

use App\Models\ObjectPrison;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class ObjectsTable extends DataTableComponent
{
    public $selectedDate;
    public string $tableName = 'objects';
    public array $objects = [];

    public $columnSearch = [
        'institution_name' => null,
        'obj_text' => null,
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDebugEnabled()
            ->setAdditionalSelects(['object_prison.id as id'])
            ->setReorderEnabled()
            ->setHideReorderColumnUnlessReorderingEnabled()
            ->setTableAttributes([
                'class' => 'w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm',
            ])
            ->setTheadAttributes([
                'class' => 'bg-gray-100 dark:bg-zinc-900',
            ])
            ->setThAttributes(function (Column $column) {
                return [
                    'class' => 'py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center',
                ];
            })
            ->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
                return [
                    'class' => 'py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center',
                ];
            })
            ->setSecondaryHeaderTrAttributes(function ($rows) {
                return ['class' => 'bg-gray-100'];
            })
            ->setSecondaryHeaderTdAttributes(function (Column $column, $rows) {
                if ($column->isField('id')) {
                    return ['class' => 'text-red-500'];
                }
                return ['default' => true];
            })
            ->setFooterTrAttributes(function ($rows) {
                return ['class' => 'bg-gray-100'];
            })
            ->setFooterTdAttributes(function (Column $column, $rows) {
                if ($column->isField('institution_name')) {
                    return ['class' => 'text-green-500'];
                }
                return ['default' => true];
            })
            ->setHideBulkActionsWhenEmptyEnabled();
    }

    public function columns(): array
    {
        return [
            Column::make('Order', 'sort')
                ->sortable()
                ->collapseOnMobile()
                ->excludeFromColumnSelect(),
            Column::make('Data', 'data')
                ->sortable()
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('d.m.Y')),
            Column::make('Instituția', 'institution.name')
                ->sortable()
                ->searchable()
                ->secondaryHeader(function () {
                    return view('tables.cells.input-search', ['field' => 'institution_name', 'columnSearch' => $this->columnSearch]);
                })
                ->footer(function ($rows) {
                    return '<strong>Total Instituții</strong>';
                })
                ->html(),
            Column::make('Eveniment', 'eveniment')
                ->sortable()
                ->format(fn($value) => $value ?? 'Depistare'),
            Column::make('Obiecte', 'id')
                ->label(function ($row) {
                    return $row->objectListItems->map(fn($item) => "{$item->name} ({$item->pivot->quantity})")->implode(', ');
                }),
            Column::make('Total Obiecte', 'id')
                ->label(fn($row) => $row->objectListItems->sum('pivot.quantity')),
            Column::make('Conținut', 'obj_text')
                ->sortable()
                ->searchable()
                ->secondaryHeader(function () {
                    return view('tables.cells.input-search', ['field' => 'obj_text', 'columnSearch' => $this->columnSearch]);
                })
                ->format(fn($value) => $value ?? '-')
                ->html(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Eveniment', 'eveniment')
                ->options([
                    '' => 'Toate',
                    'Depistare' => 'Depistare',
                    'Contracarare' => 'Contracarare',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('eveniment', $value);
                    }
                }),
            DateFilter::make('Data de la')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('data', '>=', $value);
                }),
            DateFilter::make('Data până la')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('data', '<=', $value);
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = ObjectPrison::query()
            ->with(['institution', 'objectListItems']);

        if ($this->selectedDate) {
            $query->whereDate('data', $this->selectedDate);
        }

        return $query->when($this->columnSearch['institution_name'] ?? null, fn ($query, $name) => $query->whereHas('institution', fn ($q) => $q->where('name', 'like', '%' . $name . '%')))
                     ->when($this->columnSearch['obj_text'] ?? null, fn ($query, $text) => $query->where('obj_text', 'like', '%' . $text . '%'));
    }

    public function bulkActions(): array
    {
        return [
            'delete' => 'Șterge',
        ];
    }

    public function delete()
    {
        ObjectPrison::whereIn('id', $this->getSelected())->delete();
        $this->clearSelected();
    }

    public function reorder($items): void
    {
        foreach ($items as $item) {
            ObjectPrison::find((int)$item['value'])->update(['sort' => (int)$item['order']]);
        }
    }
}