<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
        Manager Obiecte Interzise - {{ Auth::user()->institution ? Auth::user()->institution->name : 'N/A' }}
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
                <flux:button wire:click="setTab('user-objects')" 
                             class="inline-flex items-center justify-center p-4 border-b-2 {{ $activeTab === 'user-objects' ? 'text-blue-600 border-blue-600 dark:text-zinc-500 dark:border-zinc-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }} group"
                             icon="user"
                             style="border-radius: 0;"
                             variant="ghost">
                    Obiectele Mele
                </flux:button>
            </li>
            <li class="me-2">
                <flux:button wire:click="setTab('all-objects')" 
                             class="inline-flex items-center justify-center p-4 border-b-2 {{ $activeTab === 'all-objects' ? 'text-zinc-600 border-blue-600 dark:text-zinc-500 dark:border-zinc-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }} group"
                             icon="list-bullet"
                             style="border-radius: 0;"
                             variant="ghost">
                    Toate Obiectele
                </flux:button>
            </li>
        </ul>
    </div>

    <div class="mt-4">
        @if ($activeTab === 'user-objects')
            <div>
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <label for="filterDate" class="mr-2 text-sm">Filtrează după dată:</label>
                        <input type="date" id="filterDate" wire:model.live="selectedDate" 
                               class="border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded text-sm">
                    </div>
                    <div>
                        <button wire:click="openModal" class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded">
                            Adaugă obiect
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Data</th>
                                <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Instituția</th>
                                <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Eveniment</th>
                                <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Obiecte</th>
                                <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Conținut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($userObjects as $object)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                                        {{ \Carbon\Carbon::parse($object->data)->format('d.m.Y') }}
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                                        {{ $object->institution ? $object->institution->name : 'N/A' }}
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                                        {{ $object->eveniment ?? 'Depistare' }}
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                                        @foreach ($object->objectListItems as $item)
                                            {{ $item->name }} ({{ $item->pivot->quantity }})<br>
                                        @endforeach
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">
                                        {!! $object->obj_text ?? '-' !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-2 px-4 text-center text-gray-900 dark:text-white">Niciun obiect găsit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif ($activeTab === 'all-objects')
            <div>
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <label for="filterDate" class="mr-2 text-sm">Filtrează după dată:</label>
                        <input type="date" id="filterDate" wire:model.live="selectedDate" 
                               class="border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded text-sm">
                    </div>
                    <div>
                        <button onclick="printTable()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2">Print</button>
                        <button onclick="exportToPDF()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Export PDF</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <livewire:objects-table :selectedDate="$selectedDate" />
                </div>
            </div>
        @endif
    </div>

    @if ($showModal)
    <div class="fixed inset-0 bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-gray-100 dark:bg-zinc-900 p-4 w-full max-w-lg max-h-[80vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
            <h2 class="text-lg font-bold mb-3">Crează obiect interzis</h2>
            <form wire:submit.prevent="createObject">
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Data</label>
                    <input type="text" wire:model="data" readonly
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded text-sm">
                    @error('data') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                </div>
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Tip eveniment</label>
                    <select wire:model="eveniment_type" 
                            class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 text-sm">
                        <option value="Depistare">Depistare</option>
                        <option value="Contracarare">Contracarare</option>
                    </select>
                    @error('eveniment_type') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                </div>
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Obiecte</label>
                    <button type="button" wire:click="openObjectListModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Selectează obiecte
                    </button>
                    @if (!empty($selectedObjects))
                        <div class="mt-2">
                            @foreach ($selectedObjects as $index => $object)
                                <div class="flex items-center gap-2 mb-2">
                                    <span>{{ $object['name'] }} ({{ $object['quantity'] }})</span>
                                    <button type="button" wire:click="removeSelectedObject({{ $index }})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @error('selectedObjects') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                </div>
                <div class="mb-3">
                    <label class="block mb-1 text-sm">Conținut</label>
                    <textarea wire:model="obj_text" 
                              class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 min-h-[100px] text-sm"></textarea>
                    @error('obj_text') <span class="text-red-500 text-xs">{{ $message }}</span> @endif
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" wire:click="resetForm" 
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Anulează</button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">Crează</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showObjectListModal)
    <div class="fixed inset-0 bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-gray-100 dark:bg-zinc-900 p-4 w-full max-w-lg max-h-[80vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
            <h2 class="text-lg font-bold mb-3">Selectează obiecte</h2>
            <div class="mb-3">
                @foreach ($objectLists as $objectList)
                    <div class="flex items-center gap-2 mb-2">
                        <label class="flex-1">{{ $objectList->name }}</label>
                        <input type="number" wire:model="tempQuantities.{{ $objectList->id }}" min="0" 
                               class="w-20 border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded text-sm">
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" wire:click="resetForm" 
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Anulează</button>
                <button type="button" wire:click="addAllSelectedObjects" 
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">Adaugă</button>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let allObjectsData = @json($allObjects);
            console.log('Initial allObjects:', allObjectsData);

            Livewire.on('allObjectsUpdated', (data) => {
                allObjectsData = data;
                console.log('Updated allObjects:', allObjectsData);
            });

            Livewire.on('dateUpdated', () => {
                console.log('Date updated, refreshing tables');
            });

            window.printTable = function() {
                const filterDate = document.getElementById('filterDate').value;
                const title = `Obiecte Interzise - Toate Instituțiile - ${filterDate}`;
                const table = document.querySelector('.laravel-livewire-tables');

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
                const title = `Obiecte Interzise - Toate Instituțiile - ${filterDate}`;

                if (!allObjectsData || allObjectsData.length === 0) {
                    alert('Nu există date pentru export! Verificați consola pentru detalii.');
                    console.error('allObjectsData este gol sau nedefinit:', allObjectsData);
                    return;
                }

                doc.setFontSize(18);
                doc.text(title, 148.5, 10, { align: 'center' });

                let y = 20;
                doc.setFontSize(12);

                doc.setFillColor(242, 242, 242);
                doc.rect(10, y, 40, 8, 'F');
                doc.rect(50, y, 40, 8, 'F');
                doc.rect(90, y, 40, 8, 'F');
                doc.rect(130, y, 40, 8, 'F');
                doc.rect(170, y, 40, 8, 'F');
                doc.rect(210, y, 67, 8, 'F');
                doc.setTextColor(0, 0, 0);
                doc.text('Data', 12, y + 6);
                doc.text('Instituția', 52, y + 6);
                doc.text('Eveniment', 92, y + 6);
                doc.text('Obiecte', 132, y + 6);
                doc.text('Total Obiecte', 172, y + 6);
                doc.text('Conținut', 212, y + 6);
                doc.setLineWidth(0.2);
                doc.rect(10, y, 267, 8);
                y += 8;

                allObjectsData.forEach(object => {
                    const data = object.data ? new Date(object.data).toLocaleDateString('ro-RO') : 'N/A';
                    const institution = object.institution ? object.institution.name : 'N/A';
                    const eveniment = object.eveniment || 'Depistare';
                    const totalObjects = object.objectListItems.reduce((sum, item) => sum + item.pivot.quantity, 0);
                    const objectsList = object.objectListItems && object.objectListItems.length > 0 
                        ? object.objectListItems.map(item => `${item.name} (${item.pivot.quantity})`).join(', ') 
                        : '-';
                    const objText = object.obj_text || '-';

                    doc.setFillColor(249, 250, 251);
                    const contentLines = doc.splitTextToSize(objText, 65);
                    const objectsLines = doc.splitTextToSize(objectsList, 35);
                    const contentHeight = Math.max(8, Math.max(contentLines.length, objectsLines.length) * 5);
                    doc.rect(10, y, 40, contentHeight, 'F');
                    doc.rect(50, y, 40, contentHeight, 'F');
                    doc.rect(90, y, 40, contentHeight, 'F');
                    doc.rect(130, y, 40, contentHeight, 'F');
                    doc.rect(170, y, 40, contentHeight, 'F');
                    doc.rect(210, y, 67, contentHeight, 'F');
                    doc.text(data, 12, y + 6);
                    doc.text(institution.substring(0, 20), 52, y + 6);
                    doc.text(eveniment, 92, y + 6);
                    doc.text(objectsLines, 132, y + 6);
                    doc.text(totalObjects.toString(), 172, y + 6);
                    doc.text(contentLines, 212, y + 6);
                    doc.rect(10, y, 267, contentHeight);

                    y += contentHeight;
                    if (y > 190) {
                        doc.addPage();
                        y = 10;
                    }
                });

                doc.save(`obiecte_interzise_${filterDate}.pdf`);
            };
        });
    </script>
@endpush