@can('view events 24h')
@push('styles')
    <style>
        .square-tabs {
            border-radius: 0 !important;
        }
        thead th {
            vertical-align: middle;
        }
        .content-column {
            word-break: break-all;
        }
        .action-btn {
            transition: background-color 0.3s ease;
        }
        .action-btn.disabled {
            background-color: #9ca3af !important; 
            cursor: not-allowed;
            pointer-events: none; 
            opacity: 0.7;
        }
        .action-btn .timer {
            font-size: 0.8em;
            margin-left: 5px;
        }
    </style>
@endpush
<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
        Manager Evenimente 24 H - {{ Auth::user()->institution ? Auth::user()->institution->name : 'N/A' }}
    </h1>

    @if (session()->has('message'))
        <div class="mb-4 text-green-500">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 text-red-500">{{ session('error') }}</div>
    @endif

    <div class="border-b border-gray-200 dark:border-zinc-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
            <li class="me-2">
                <flux:button wire:click="setTab('added-events')" 
                             class="inline-flex items-center justify-center p-4 border-b-2 {{ $activeTab === 'added-events' ? 'text-blue-600 border-blue-600 dark:text-zinc-500 dark:border-zinc-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }} group"
                             icon="plus-circle"
                             style="border-radius: 0;"
                             variant="ghost">
                    Evenimente Adăugate
                </flux:button>
            </li>
            <li class="me-2">
                <flux:button wire:click="setTab('all-events')" 
                             class="inline-flex items-center justify-center p-4 border-b-2 {{ $activeTab === 'all-events' ? 'text-zinc-600 border-blue-600 dark:text-zinc-500 dark:border-zinc-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }} group"
                             icon="list-bullet"
                             style="border-radius: 0;"
                             variant="ghost">
                    Evenimente Toate Instituțiile
                </flux:button>
            </li>
        </ul>
    </div>

    <div class="mt-4">
        @if ($activeTab === 'added-events')
            <div>
                <div class="mb-4">
                    @can('create events 24h')
                        <button wire:click="$set('showModal', true)" class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded">
                            Adaugă eveniment
                        </button>
                    @endcan
                </div>
                
                <div class=" max-h-[700px]" x-data="{ loading: false }" 
                     
                    @forelse ($events->sortKeysDesc() as $date => $dayEvents)
                        <h2 class="text-lg font-semibold mt-4 mb-2 text-gray-900 dark:text-white">
                            Evenimente din data {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
                        </h2>
                        <div class="mb-4">
                            <table class="w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm">
                                <thead>
                                <tr>
    <th class="w-[100px]  py-2 px-4 border-b border-r border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Data evenimentului</th>
    <th class="w-[100px]  py-2 px-4 border-b border-r border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Categoria</th>
    <th class="w-[100px] py-2 px-4 border-b border-r border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Persoane implicate</th>
    <th class="w-[1000px] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Conținut</th>
    <th class="max-w-[400px]  py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Acțiuni</th>
</tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalEventCount = $dayEvents->count();
                                        $groupedByCategory = $dayEvents->groupBy('id_events_category');
                                    @endphp
                                    @foreach ($groupedByCategory as $categoryId => $categoryEvents)
                                        @php
                                            $categoryEventCount = $categoryEvents->count();
                                            $categoryName = $categoryEvents->first()->category->name ?? '-';
                                        @endphp
                                        @foreach ($categoryEvents as $index => $event)
                                            @php
                                                $createdAt = \Carbon\Carbon::parse($event->created_at);
                                                $currentTime = \Carbon\Carbon::now();
                                                $minutesSinceCreation = $createdAt->diffInMinutes($currentTime);
                                                $canEditOrDelete = $minutesSinceCreation < 5;
                                            @endphp
                                            <tr>
                                                @if ($index === 0 && $categoryEvents === $groupedByCategory->first())
                                                    <td class="py-2 px-4 border-b border-r border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center align-center" rowspan="{{ $totalEventCount }}">
                                                        {{ \Carbon\Carbon::parse($event->data)->format('d.m.Y') }}
                                                    </td>
                                                @endif
                                                @if ($index === 0)
                                                    <td class="py-2 px-4 border-b border-r border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left align-top" rowspan="{{ $categoryEventCount }}">
                                                        {{ $categoryName }}
                                                    </td>
                                                @endif
                                                <td class="py-2 px-4 border-b border-r border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                                                    {{ $event->persons_involved ?? '-' }}
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left content-column">
                                                    {!! $event->events_text ?? '-' !!}
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-center">
                                                    @can('edit events 24h')
                                                        <button wire:click="editEvent({{ $event->id }})" 
                                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 my-8 rounded action-btn" 
                                                                data-created-at="{{ $event->created_at }}" 
                                                                style="margin-right: 3px;" 
                                                                @if(!$canEditOrDelete) disabled @endif>
                                                            Editează <span class="timer"></span>
                                                        </button>
                                                    @endcan
                                                    @can('delete events 24h')
                                                        <button wire:click="deleteEvent({{ $event->id }})" 
                                                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 my-8 rounded action-btn" 
                                                                data-created-at="{{ $event->created_at }}" 
                                                                @if(!$canEditOrDelete) disabled @endif>
                                                            Șterge <span class="timer"></span>
                                                        </button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <p class="text-center text-gray-900 dark:text-white">Niciun eveniment găsit.</p>
                    @endforelse
                </div>
            </div>
        @elseif ($activeTab === 'all-events')
            <div>
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <label for="filterDate" class="mr-2 text-sm">Filtrează după dată:</label>
                        <input type="date" id="filterDate" wire:model="filterDate" wire:change="updateFilterDate"
                               class="border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded text-sm">
                    </div>
                    <div>
                        <button onclick="printTable()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2">Print</button>
                        <button onclick="exportToPDF()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Export PDF</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="all-events-table" class="w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm">
                        <thead>
                            <tr>
                                <th style="width: 148px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Categoria</th>
                                <th style="width: 148px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Instituția</th>
                                <th style="width: 160px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Persoane implicate</th>
                                <th style="width: 1270px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Conținut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allEvents as $categoryData)
                                <tr>
                                    <td class="py-2 px-4 border-b border-r border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left align-top" rowspan="{{ $categoryData['events']->count() }}">
                                        {{ $categoryData['category_name'] }}
                                    </td>
                                    @foreach ($categoryData['events'] as $index => $event)
                                        @if ($index > 0)
                                            <tr>
                                        @endif
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">
                                            {{ $event->institution ? $event->institution->name : 'N/A' }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                                            {{ $event->persons_involved ?? '0' }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left content-column">
                                            {!! $event->events_text ?? '-nu a fost adaugat' !!}
                                        </td>
                                        </tr>
                                    @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="py-2 px-4 text-center text-gray-900 dark:text-white">Niciun eveniment găsit pentru data selectată.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    @if ($showModal)
    <div class="fixed top-0 bottom-0 left-0 right-0 lg:left-[16rem] bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-gray-100 dark:bg-zinc-900 p-6 w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
            <h2 class="text-lg font-bold mb-3">{{ $editingEventId ? 'Editează eveniment' : 'Crează eveniment' }}</h2>
            <form wire:submit.prevent="{{ $editingEventId ? 'updateEvent' : 'createEvent' }}">
                <div class="mb-3 flex flex-row space-x-2">
                    <div class="flex-1 w-1/4">
                        <label class="block mb-1 text-sm">Data</label>
                        @if($editingEventId && Auth::user()->can('schimbare data evenimente'))
                            <input type="date" wire:model="data" 
                                   class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @else
                            <input type="text" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" readonly
                                   class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded text-sm">
                        @endif
                        @error('data') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Categorie</label>
                    <select wire:model="id_events_category" wire:change="updateSubcategories"
                            class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        <option value="">Selectează o categorie</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('id_events_category') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                </div>
                @if ($subcategories->isNotEmpty())
                    <div class="mb-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($subcategories as $subcategory)
                                <label class="flex items-center w-1/3">
                                    <input type="checkbox" wire:model="id_subcategory" value="{{ $subcategory->id }}"
                                           class="form-checkbox h-4 w-4 text-zinc-600">
                                    <span class="ml-2 text-sm">{{ $subcategory->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('id_subcategory') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                    </div>
                @endif
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Persoane implicate</label>
                    <input type="number" wire:model="persons_involved" min="0" 
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                    @error('persons_involved') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                </div>
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Conținut</label>
                    <textarea wire:model="events_text" 
                              class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 min-h-[150px] text-sm"></textarea>
                    @error('events_text') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" wire:click="resetForm" 
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Anulează</button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                        {{ $editingEventId ? 'Actualizează' : 'Crează' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@else
    <div class="text-center text-gray-900 dark:text-white">
        <p>Nu aveți permisiunea de a vizualiza evenimentele.</p>
    </div>
@endcan

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let allEventsData = @json($allEvents);
            console.log('Initial allEvents:', allEventsData);

            Livewire.on('allEventsUpdated', (data) => {
                allEventsData = data;
                console.log('Updated allEvents:', allEventsData);
            });

            Livewire.on('eventsUpdated', () => {
                console.log('Events updated');
                updateActionButtons();
            });

            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${minutes}:${secs < 10 ? '0' + secs : secs}`;
            }

            function updateActionButtons() {
                const actionButtons = document.querySelectorAll('.action-btn:not(.disabled)');
                actionButtons.forEach(button => {
                    const createdAt = new Date(button.getAttribute('data-created-at'));
                    const now = new Date();
                    const timeDiff = (now - createdAt) / 1000;
                    const maxTime = 5 * 60;
                    const timerSpan = button.querySelector('.timer');

                    if (button.countdown) {
                        clearInterval(button.countdown);
                    }

                    if (timeDiff >= maxTime) {
                        button.disabled = true;
                        button.classList.add('disabled');
                        timerSpan.textContent = '(expirat)';
                    } else {
                        let timeLeft = maxTime - timeDiff;
                        timerSpan.textContent = `(${formatTime(timeLeft)})`;

                        button.countdown = setInterval(() => {
                            timeLeft--;
                            if (timeLeft <= 0) {
                                clearInterval(button.countdown);
                                button.disabled = true;
                                button.classList.add('disabled');
                                timerSpan.textContent = '(expirat)';
                            } else {
                                timerSpan.textContent = `(${formatTime(timeLeft)})`;
                            }
                        }, 1000);
                    }
                });
            }

            updateActionButtons();

            window.printTable = function() {
                const filterDate = document.getElementById('filterDate').value;
                const title = `Nota informativă 24H la data de ${filterDate}`;
                const table = document.getElementById('all-events-table');

                if (!table) {
                    alert('Tabelul nu a fost găsit!');
                    return;
                }

                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; background-color: #fff; }
                                h1 { font-size: 18px; text-align: center; margin-bottom: 20px; }
                                table { width: 100%; border-collapse: collapse; font-size: 12px; background-color: #f9fafb; }
                                th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; vertical-align: middle; }
                                th { background-color: #f2f2f2; }
                                td[rowspan] { border-right: 1px solid #d1d5db; vertical-align: top; }
                                th:nth-child(1), td:nth-child(1) { width: 148px; }
                                th:nth-child(2), td:nth-child(2) { width: 148px; }
                                th:nth-child(3), td:nth-child(3) { width: 160px; }
                                th:nth-child(4), td:nth-child(4) { width: 1270px; word-break: break-all; }
                            </style>
                        </head>
                        <body>
                            <h1>${title}</h1>
                            ${table.outerHTML}
                        </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            };

            window.exportToPDF = function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape' });
                const filterDate = document.getElementById('filterDate').value;
                const title = `Nota informativă 24H la data de ${filterDate}`;

                if (!allEventsData || allEventsData.length === 0) {
                    alert('Nu există date pentru export! Verificați consola pentru detalii.');
                    console.error('allEventsData este gol sau nedefinit:', allEventsData);
                    return;
                }

                doc.setFontSize(18);
                doc.text(title, 148.5, 10, { align: 'center' });

                let y = 20;
                doc.setFontSize(12);

                doc.setFillColor(242, 242, 242);
                doc.rect(10, y, 40, 8, 'F');
                doc.rect(50, y, 40, 8, 'F');
                doc.rect(90, y, 20, 8, 'F');
                doc.rect(110, y, 167, 8, 'F');
                doc.setTextColor(0, 0, 0);
                doc.text('Categoria', 12, y + 6);
                doc.text('Instituția', 52, y + 6);
                doc.text('Pers.', 92, y + 6);
                doc.text('Conținut', 112, y + 6);
                doc.setLineWidth(0.2);
                doc.rect(10, y, 267, 8);
                y += 8;

                allEventsData.forEach(categoryData => {
                    const categoryName = categoryData.category_name || 'N/A';
                    const events = categoryData.events || [];
                    const rowSpan = events.length;

                    if (rowSpan === 0) {
                        doc.setFillColor(249, 250, 251);
                        doc.rect(10, y, 40, 8, 'F');
                        doc.rect(50, y, 40, 8, 'F');
                        doc.rect(90, y, 20, 8, 'F');
                        doc.rect(110, y, 167, 8, 'F');
                        doc.text(categoryName, 12, y + 6);
                        doc.text('N/A', 52, y + 6);
                        doc.text('-', 92, y + 6);
                        doc.text('N/A', 112, y + 6);
                        doc.rect(10, y, 267, 8);
                        y += 8;
                    } else {
                        let categoryHeight = rowSpan * 8;
                        events.forEach((event, index) => {
                            const institution = event.institution ? event.institution.name : 'N/A';
                            const persons = event.persons_involved !== null ? event.persons_involved.toString() : '-';
                            const content = event.events_text ? event.events_text.replace(/<[^>]+>/g, '') : '-';

                            doc.setFillColor(249, 250, 251);
                            const contentLines = doc.splitTextToSize(content, 165);
                            const contentHeight = Math.max(8, contentLines.length * 5);
                            doc.rect(50, y, 40, contentHeight, 'F');
                            doc.rect(90, y, 20, contentHeight, 'F');
                            doc.rect(110, y, 167, contentHeight, 'F');
                            doc.text(institution.substring(0, 20), 52, y + 6);
                            doc.text(persons, 92, y + 6);
                            doc.text(contentLines, 112, y + 6);
                            doc.rect(50, y, 40, contentHeight);
                            doc.rect(90, y, 20, contentHeight);
                            doc.rect(110, y, 167, contentHeight);

                            if (index === 0) {
                                doc.rect(10, y, 40, categoryHeight, 'F');
                                doc.text(categoryName, 12, y + 6);
                                doc.rect(10, y, 40, categoryHeight);
                            }

                            y += contentHeight;
                            if (y > 190) {
                                doc.addPage();
                                y = 10;
                            }
                        });
                    }

                    if (y > 190) {
                        doc.addPage();
                        y = 10;
                    }
                });

                doc.save(`nota_informativa_24h_${filterDate}.pdf`);
            };
        });
    </script>
@endpush