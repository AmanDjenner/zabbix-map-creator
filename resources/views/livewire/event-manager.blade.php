@can('view events')
<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager Evenimente</h1>

    <div class="mb-4">
        @can('create events')
            <button wire:click="$set('showModal', true)" 
                    class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded">
                Adaugă eveniment
            </button>
        @endcan
    </div>

    @if (session()->has('message'))
        <div class="mb-4 text-green-500">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 text-red-500">{{ session('error') }}</div>
    @endif

    <!-- Filtre și căutare -->
    <div class="w-full mb-4 flex flex-wrap items-center gap-4">
    <div class="flex items-center">
        <input type="date" wire:model.live="startDate" 
               class="border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded">
        @error('startDate') <span class="text-red-500 ml-2">{{ $message }}</span> @enderror
    </div>
    <div class="flex items-center">
        <label class="mr-2 text-gray-900 dark:text-white">---</label>
        <input type="date" wire:model.live="endDate" 
               class="border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded">
        @error('endDate') <span class="text-red-500 ml-2">{{ $message }}</span> @enderror
    </div>
    <div class="w-full flex items-center flex-grow">
        <input type="text" wire:model.live="search" placeholder="Caută în detalii..." 
               class="w-[80%] border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded">
    </div>
    <div class="flex items-center">
        <label class="mr-2 text-gray-900 dark:text-white">Elemente pe pagină:</label>
        <select wire:model.live="perPage" class="border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded">
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
</div>

    <div class="flex justify-center">
        <table class="bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th wire:click="sortBy('id')" class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white cursor-pointer text-center">
                        Nr. Ordine 
                        @if ($sortField === 'id') 
                            ({{ $sortDirection === 'asc' ? '↑' : '↓' }})
                        @endif
                    </th>
                    <th wire:click="sortBy('data')" class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white cursor-pointer text-center">
                        Data 
                        @if ($sortField === 'data') 
                            ({{ $sortDirection === 'asc' ? '↑' : '↓' }})
                        @endif
                    </th>
                    <th wire:click="sortBy('id_institution')" class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white cursor-pointer text-center">
                        Instituție 
                        @if ($sortField === 'id_institution') 
                            ({{ $sortDirection === 'asc' ? '↑' : '↓' }})
                        @endif
                    </th>
                    <th wire:click="sortBy('persons_involved')" class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white cursor-pointer text-center">
                        Persoane 
                        @if ($sortField === 'persons_involved') 
                            ({{ $sortDirection === 'asc' ? '↑' : '↓' }})
                        @endif
                    </th>
                    <th class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Detalii</th>
                    <th class="w-[200px] py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $key => $event)
                    <tr>
                        <td class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                            {{ ($events->perPage() * ($events->currentPage() - 1)) + $key + 1 }}
                        </td>
                        <td class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                            {{ $event->data ? $event->data->format('d.m.Y') : '-' }}
                        </td>
                        <td class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                            {{ $event->institution->name ?? '-' }}
                        </td>
                        <td class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                            {{ $event->persons_involved ?? '-' }}
                        </td>
                        <td class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">
                            {!! $event->events_text ?? '-' !!}
                        </td>
                        <td class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-center">
                            @can('edit events')
                                <button wire:click="editEvent({{ $event->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete events')
                                <button wire:click="deleteEvent({{ $event->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-2 px-4 border border-gray-300 dark:border-zinc-700 text-center text-gray-900 dark:text-white">Niciun eveniment găsit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-center">
        {{ $events->links() }}
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center" wire:ignore.self>
            <div class="bg-gray-100 dark:bg-zinc-900 p-6 w-1/2 text-gray-900 dark:text-white border border-gray-300 dark:border-zinc-700">
                <h2 class="text-xl mb-4">{{ $editingEventId ? 'Editează eveniment' : 'Crează eveniment' }}</h2>
                <form wire:submit.prevent="{{ $editingEventId ? 'updateEvent' : 'createEvent' }}">
                    <div class="mb-4">
                        <label class="block mb-1">Data</label>
                        <input type="date" id="data-input" wire:model="data" 
                               class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        @error('data') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1">Instituție</label>
                        <select wire:model="id_institution" 
                                class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500">
                            <option value="">Selectează o instituție</option>
                            @foreach ($institutions as $institution)
                                <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                            @endforeach
                        </select>
                        @error('id_institution') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1">Persoane implicate</label>
                        <input type="number" wire:model="persons_involved" min="0" 
                               class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        @error('persons_involved') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4" wire:ignore>
                        <label class="block mb-1">Detalii</label>
                        <textarea id="tiny-editor" wire:model.debounce.500ms="events_text" 
                                  class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 min-h-[200px]"></textarea>
                        @error('events_text') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" wire:click="resetForm" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            {{ $editingEventId ? 'Actualizează' : 'Crează' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                function initializeTinyMCE() {
                    if (tinymce.get('tiny-editor')) {
                        tinymce.get('tiny-editor').remove();
                    }
                    tinymce.init({
                        selector: '#tiny-editor',
                        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                        height: 300,
                        content_style: 'body { font-family: Instrument Sans, sans-serif; } .mce-content-body { padding: 10px; }',
                        skin: 'oxide',
                        skin_url: '/js/tinymce/skins/ui/oxide',
                        content_css: '/js/tinymce/skins/content/default/content.css',
                        setup: (editor) => {
                            editor.on('init', () => {
                                editor.setContent(@json($events_text) || '');
                            });
                            editor.on('Change Input Undo Redo', () => {
                                @this.set('events_text', editor.getContent());
                            });
                        }
                    });
                }

                Livewire.on('showModal', () => {
                    setTimeout(() => {
                        initializeTinyMCE();
                    }, 100);
                });

                Livewire.on('editEvent', (eventData) => {
                    if (eventData && eventData.length > 0) {
                        const data = eventData[0].data;
                        const dateInput = document.getElementById('data-input');
                        if (dateInput && data.data) {
                            const formattedDate = new Date(data.data).toISOString().split('T')[0];
                            dateInput.value = formattedDate;
                        }
                        setTimeout(() => {
                            if (tinymce.get('tiny-editor')) {
                                tinymce.get('tiny-editor').setContent(data.events_text || '');
                            }
                        }, 500);
                    }
                });
            });
        </script>
    @endpush
</div>
@else
    <div class="text-center text-gray-900 dark:text-white">
        <p>Nu aveți permisiunea de a vizualiza evenimentele.</p>
    </div>
@endcan