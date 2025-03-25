@push('styles')
    <style>
        .square-tabs {
            border-radius: 0 !important;
        }
    </style>
@endpush

<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
        Statistici Deținuți - {{ Auth::user()->institution ? Auth::user()->institution->name : 'N/A' }}
    </h1>

    <div class="border-b border-gray-200 dark:border-zinc-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
            <li class="me-2">
                <flux:button wire:click="setTab('raw-data')" 
                             class="inline-flex items-center justify-center p-4 border-b-2 {{ $activeTab === 'raw-data' ? 'text-blue-600 border-blue-600 dark:text-zinc-500 dark:border-zinc-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }} group square-tabs"
                             icon="plus-circle"
                             style="border-radius: 0;"
                             variant="ghost">
                    Date adăugate
                </flux:button>
            </li>
            <li class="me-2">
                <flux:button wire:click="setTab('statistics')" 
                             class="inline-flex items-center justify-center p-4 border-b-2 {{ $activeTab === 'statistics' ? 'text-blue-600 border-blue-600 dark:text-zinc-500 dark:border-zinc-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }} group square-tabs"
                             icon="list-bullet"
                             style="border-radius: 0;"
                             variant="ghost">
                    Deținuți în toate Instituțiile
                </flux:button>
            </li>
        </ul>
    </div>

   
    <div class="mt-4">
        @if ($activeTab === 'raw-data')
            <div class="mb-4 flex justify-between items-center">
                <div>
                    @if($canAddDetinuti)
                        <button wire:click="openAddModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Adaugă Deținuți
                        </button>
                    @else
                        <button class="bg-gray-500 text-white font-bold py-2 px-4 rounded" disabled>
                            Următoarea adăugare în: <span wire:poll.1000ms>{{ $timeUntilNextAdd }}</span>
                        </button>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                @if (session()->has('message'))
                    <div class="mb-4 text-green-500">{{ session('message') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="mb-4 text-red-500">{{ session('error') }}</div>
                @endif
                <table class="w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm">
                    <thead>
                        <tr>
                            <!-- <th class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Instituție</th> -->
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Total</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Deținuți reali</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">În căutare</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Detenție preventivă</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Condiții inițiale</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Pe viață</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Femei</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Minori</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Sector deschis</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Fără escortă</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Brățări</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Grevă foame</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Izolator</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Spitale</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">IP spitale</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">DDS spitale</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Muncă ext.</th>
                            <th style="max-width:70px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">IP ext.</th>
                            <th style="max-width:200px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($detinuti as $detinut)
                            <tr>
                                <!-- <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->institution->name ?? '-' }}</td> -->
                                <td style="width:100px" class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->total ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->real_inmates ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->in_search ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->pretrial_detention ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->initial_conditions ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->life ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->female ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->minors ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->open_sector ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->no_escort ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->monitoring_bracelets ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->hunger_strike ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->disciplinary_insulator ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->admitted_to_hospitals ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->employed_ip_in_hospitals ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->employed_dds_in_hospitals ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->work_outside ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $detinut->employed_ip_work_outside ?? '-' }}</td>
                                <td class="py-1 px-2 border-b border-gray-300 dark:border-zinc-700 text-center">
                                    @can('edit detinuti')
                                        <button wire:click="editDetinut({{ $detinut->id }})" 
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded" style="margin-right: 3px;">Editează</button>
                                    @endcan
                                    @can('delete detinuti')
                                        <button wire:click="deleteDetinut({{ $detinut->id }})" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="py-2 px-4 text-center text-gray-900 dark:text-white">Nicio înregistrare găsită pentru data curentă.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif ($activeTab === 'statistics')
            <div id="statistics-table" class="overflow-x-auto">
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <label for="selected-date" class="block mb-1 text-gray-900 dark:text-white">Alege data:</label>
                        <input type="date" id="selected-date" wire:model.live="statisticsDate" 
                               class="w-48 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500">
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="printTable()" class="bg-blue-500 hover:bg-zinc-700 text-white font-bold py-2 px-4 rounded">
                            Printează
                        </button>
                        <button onclick="downloadPDF()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Descarcă PDF
                        </button>
                    </div>
                </div>
                @if (session()->has('message'))
                    <div class="mb-4 text-green-500">{{ session('message') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="mb-4 text-red-500">{{ session('error') }}</div>
                @endif
                @if ($statisticsDate)
                    @if (count($statistics) > 0)
                        <h2 class="text-xl font-bold text-center mb-4 hidden" id="print-title">
                            Statistica deținuți 24 ore pentru data de {{ Carbon\Carbon::parse($statisticsDate)->format('d-m-Y') }}
                        </h2>
                        <table class="w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Nr.</th>
                                    <th style="width: 160px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">Indicatori</th>
                                    @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 18] as $i)
                                        <th style="width: 70px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">P{{ $i }}</th>
                                    @endforeach
                                    <th style="width: 70px;" class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($indicators as $key => $label)
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $loop->iteration }}</td>
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">{{ $label }}</td>
                                        @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 18] as $i)
                                            <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                                                {{ $statistics[$i][$key] ?? 0 }}
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center font-bold">
                                            {{ array_sum(array_column($statistics, $key)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-900 dark:text-white text-center mt-6">Nu există date statistice pentru data selectată.</p>
                    @endif
                @else
                    <p class="text-gray-900 dark:text-white text-center mt-6">Selectați o dată pentru a afișa statisticile.</p>
                @endif
            </div>
        @endif
    </div>

<!-- Modal for Adding/Editing Detinuti -->
@if($showAddModal)
    <div class="fixed top-0 bottom-0 left-0 right-0 lg:left-[16rem] bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-gray-100 dark:bg-zinc-900 p-6 w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
            <h2 class="text-lg font-bold mb-3">{{ $editingDetinutId ? 'Editează înregistrare' : 'Crează înregistrare' }}</h2>
            <form wire:submit.prevent="storeDetinuti">
                <!-- Date Field (Full Width) -->
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Data (astăzi)</label>
                    <input type="text" value="{{ Carbon\Carbon::today()->format('Y-m-d') }}" readonly
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded text-sm">
                    <input type="hidden" wire:model="data" value="{{ now()->format('Y-m-d') }}">
                </div>

                <!-- Three-Column Layout with Automatic Distribution -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Total</label>
                        <input type="number" wire:model="total" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('total') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Deținuți reali</label>
                        <input type="number" wire:model="real_inmates" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('real_inmates') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">În căutare</label>
                        <input type="number" wire:model="in_search" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('in_search') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Detenție preventivă</label>
                        <input type="number" wire:model="pretrial_detention" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('pretrial_detention') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Condiții inițiale</label>
                        <input type="number" wire:model="initial_conditions" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('initial_conditions') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Pe viață</label>
                        <input type="number" wire:model="life" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('life') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Femei</label>
                        <input type="number" wire:model="female" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('female') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Minori</label>
                        <input type="number" wire:model="minors" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('minors') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Sector deschis</label>
                        <input type="number" wire:model="open_sector" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('open_sector') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Fără escortă</label>
                        <input type="number" wire:model="no_escort" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('no_escort') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Brățări monitorizare</label>
                        <input type="number" wire:model="monitoring_bracelets" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('monitoring_bracelets') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Grevă foame</label>
                        <input type="number" wire:model="hunger_strike" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('hunger_strike') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Izolator disciplinar</label>
                        <input type="number" wire:model="disciplinary_insulator" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('disciplinary_insulator') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Internați spitale</label>
                        <input type="number" wire:model="admitted_to_hospitals" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('admitted_to_hospitals') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Angajați IP spitale</label>
                        <input type="number" wire:model="employed_ip_in_hospitals" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('employed_ip_in_hospitals') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Angajați DDS spitale</label>
                        <input type="number" wire:model="employed_dds_in_hospitals" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('employed_dds_in_hospitals') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Muncă exterior</label>
                        <input type="number" wire:model="work_outside" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('work_outside') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="block mb-1 text-sm">Angajați IP exterior</label>
                        <input type="number" wire:model="employed_ip_work_outside" min="0" 
                               class="w-32 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        @error('employed_ip_work_outside') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" wire:click="closeAddModal" 
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Anulează</button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                        {{ $editingDetinutId ? 'Actualizează' : 'Crează' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.3/jspdf.plugin.autotable.min.js"></script>
    <script src="{{ asset('js/roboto.js') }}"></script>

    <script>
        const indicators = @json($indicators);
        const institutionIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 18];

        window.printTable = function() {
            const filterDate = document.getElementById('selected-date').value;
            const title = `Statistica deținuți 24 ore pentru data de ${filterDate.split('-').reverse().join('-')}`;
            const table = document.querySelector('#statistics-table table');

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
                            body { font-family: Arial, sans-serif; margin: 0; padding: 10mm; background-color: #fff; }
                            h1 { font-size: 18px; text-align: center; margin-bottom: 10px; }
                            table { width: 100%; border-collapse: collapse; font-size: 12px; background-color: #f9fafb; }
                            th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
                            th { background-color: #f2f2f2; }
                            td[rowspan] { border-right: 1px solid #d1d5db; vertical-align: top; }
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

        function downloadPDF() {
            try {
                const selectedDateInput = document.getElementById('selected-date').value;
                if (!selectedDateInput) {
                    throw new Error('Vă rugăm să selectați o dată.');
                }
                const formattedDate = selectedDateInput.split('-').reverse().join('-');

                const table = document.querySelector('#statistics-table table');
                if (!table) {
                    throw new Error('Tabelul nu este disponibil.');
                }

                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4'
                });

                doc.addFileToVFS('Roboto-Regular.ttf', robotoFont); 
                doc.addFont('Roboto-Regular.ttf', 'Roboto', 'regular');
                doc.setFont('Roboto');

                doc.setFontSize(16);
                const title = `Statistica deținuți 24 ore pentru data de ${formattedDate}`;
                doc.text(title, 148.5, 20, { align: 'center' });

                const headers = ['Nr.', 'Indicatori'].concat(institutionIds.map(id => `P${id}`), ['Total']);
                const body = [];

                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const rowData = Array.from(cells).map(cell => cell.textContent.trim());
                    body.push(rowData);
                });

                doc.autoTable({
                    head: [headers],
                    body: body,
                    startY: 30,
                    styles: { font: 'Roboto', fontSize: 8, cellPadding: 2, overflow: 'linebreak', halign: 'center' },
                    headStyles: { font: 'Roboto', fillColor: [100, 100, 100], textColor: [255, 255, 255], fontStyle: 'bold', halign: 'center' },
                    columnStyles: { 0: { cellWidth: 10 }, 1: { cellWidth: 40, halign: 'left' } },
                    margin: { top: 10, left: 10, right: 10, bottom: 10 },
                });

                doc.save(`Sinteza pentru data de ${formattedDate}.pdf`);
            } catch (error) {
                console.error('Eroare la generarea PDF-ului:', error);
                alert(error.message || 'A apărut o eroare la generarea PDF-ului.');
            }
        }
    </script>
</div>