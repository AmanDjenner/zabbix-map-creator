@can('view objects')
<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Lista obiectelor interzise</h1>

    <div class="mb-4">
        @can('create objects')
            <button wire:click="$set('showModal', true)" 
                    class="bg-blue-500 hover:bg-zinc-600 text-white font-bold py-2 px-4 rounded">
                Adaugă obiect
            </button>
        @endcan
    </div>

    @if (session()->has('error'))
        <div class="mb-4 text-red-500">{{ session('error') }}</div>
    @endif

    @if (session()->has('message'))
        <div class="mb-4 text-green-500">{{ session('message') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 text-sm">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Nr.</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">Nume</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($objectLists as $index => $objectList)
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $index + 1 }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">{{ $objectList->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">
                            @can('edit objects')
                                <button wire:click="editObjectList({{ $objectList->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete objects')
                                <button wire:click="deleteObjectList({{ $objectList->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-2 px-4 text-center text-gray-900 dark:text-white">Niciun obiect găsit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal pentru creare/editare obiect -->
    @if ($showModal)
    <div class="fixed top-0 bottom-0 left-0 right-0 lg:left-[16rem] bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
                <h2 class="text-xl mb-4">{{ $editingObjectListId ? 'Editează obiect' : 'Crează obiect' }}</h2>
                <form wire:submit.prevent="{{ $editingObjectListId ? 'updateObjectList' : 'createObjectList' }}">
                    <div class="mb-4">
                        <label class="block mb-1">Nume</label>
                        <input type="text" wire:model="name" 
                               class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500">
                        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" wire:click="resetForm" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            {{ $editingObjectListId ? 'Actualizează' : 'Crează' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@else
    <div class="text-center text-gray-900 dark:text-white">
        <p>Nu aveți permisiunea de a vizualiza lista de obiecte.</p>
    </div>
@endcan