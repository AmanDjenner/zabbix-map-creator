<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager Leziuni</h1>

    <div class="mb-4">
        @can('create injuries')
            <button wire:click="$set('showModal', true)" 
                    class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded">
                Adaugă leziune
            </button>
        @endcan
    </div>

    <div class="flex justify-center">
        <table class="w-[70%] bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Data</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Instituție</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Categorie Leziuni</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Persoane implicate</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Detalii</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($injuries as $injury)
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{{ $injury->data ? $injury->data->format('d-m-Y') : '-' }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{{ $injury->institution->name ?? '-' }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{{ $injury->injuryCategory->name ?? '-' }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{{ $injury->persons_involved ?? '-' }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{!! $injury->injuries_text ?? '-' !!}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700">
                            @can('edit injuries')
                                <button wire:click="editInjury({{ $injury->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete injuries')
                                <button wire:click="deleteInjury({{ $injury->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-2 px-4 text-center text-gray-900 dark:text-white">Nicio leziune găsită.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center" wire:ignore.self>
            <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-1/2 text-gray-900 dark:text-white border border-gray-300 dark:border-zinc-700">
                <h2 class="text-xl mb-4">{{ $editingInjuryId ? 'Editează leziune' : 'Crează leziune' }}</h2>
                <form wire:submit.prevent="{{ $editingInjuryId ? 'updateInjury' : 'createInjury' }}">
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
                        <label class="block mb-1">Categorie Leziuni</label>
                        <select wire:model="id_injuries_category" 
                                class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500">
                            <option value="">Selectează o categorie</option>
                            @foreach ($injuryCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('id_injuries_category') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1">Persoane implicate</label>
                        <input type="number" wire:model="persons_involved" min="0" 
                               class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        @error('persons_involved') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4" wire:ignore>
                        <label class="block mb-1 text-gray-900 dark:text-white">Detalii</label>
                        <div class="mb-2 p-3 bg-gray-200 dark:bg-zinc-700 text-gray-900 dark:text-white  shadow-md">
                            <p class="text-sm font-semibold">Adaugă detalii</p>
                        </div>
                        <textarea id="tiny-editor" wire:model.debounce.500ms="injuries_text" 
                                  class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 min-h-[200px]"></textarea>
                        @error('injuries_text') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" wire:click="resetForm" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            {{ $editingInjuryId ? 'Actualizează' : 'Crează' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM fully loaded');
                if (typeof tinymce === 'undefined') {
                    console.error('TinyMCE nu este încărcat. Verifică calea în app.blade.php.');
                    return;
                }
                console.log('TinyMCE este încărcat.');

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
                                console.log('TinyMCE initialized');
                                editor.setContent(@json($injuries_text) || '');
                            });
                            editor.on('Change Input Undo Redo', () => {
                                const content = editor.getContent();
                                console.log('TinyMCE content updated:', content);
                                @this.set('injuries_text', content);
                            });
                        }
                    });
                }

                Livewire.on('showModal', () => {
                    console.log('Show modal event triggered');
                    setTimeout(() => {
                        initializeTinyMCE();
                    }, 100);
                });

                Livewire.on('editInjury', (event) => {
                    console.log('Edit event received:', event);
                    if (event && event.length > 0) {
                        const data = event[0];
                        const dateInput = document.getElementById('data-input');
                        if (dateInput) {
                            const formattedDate = data.data ? new Date(data.data).toISOString().split('T')[0] : '';
                            dateInput.value = formattedDate;
                            console.log('Data set:', formattedDate);
                        } else {
                            console.error('Date input not found');
                        }

                        if (tinymce.get('tiny-editor')) {
                            tinymce.get('tiny-editor').setContent(data.injuries_text || '');
                            console.log('TinyMCE content set:', data.injuries_text);
                        } else {
                            console.warn('TinyMCE not initialized yet, retrying...');
                            setTimeout(() => {
                                if (tinymce.get('tiny-editor')) {
                                    tinymce.get('tiny-editor').setContent(data.injuries_text || '');
                                    console.log('TinyMCE content set after delay:', data.injuries_text);
                                }
                            }, 500);
                        }
                    }
                });
            });
        </script>
    @endpush
</div>