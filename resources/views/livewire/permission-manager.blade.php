<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager de permisiuni</h1>

    <!-- Buton pentru a deschide modalul de creare -->
    @can('create permissions')
        <button wire:click="$set('showModal', true)" 
                class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded mb-4">
            Adaugă permisiune
        </button>
    @endcan

    <!-- Tabel cu permisiuni -->
    <div class="flex justify-center">
        <table class="w-[90%] bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">Nume</th>
                    <th class="w-[200] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">{{ $permission->name }}</td>
                        <td class="w-[200] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-center">
                            @can('edit permissions')
                                <button wire:click="editPermission({{ $permission->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete permissions')
                                <button wire:click="deletePermission({{ $permission->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

   
    @if ($showModal)
    <div class="fixed top-0 bottom-0 left-0 right-0 lg:left-[16rem] bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700 mt-4">
            <h2 class="text-xl mb-4 sticky top-0 bg-gray-100 dark:bg-zinc-900 z-10">{{ $editingPermissionId ? 'Editează permisiune' : 'Crează permisiune' }}</h2>
            <form wire:submit.prevent="{{ $editingPermissionId ? 'updatePermission' : 'createPermission' }}">
                <div class="mb-4">
                    <label class="block mb-1">Permisiunea</label>
                    <input type="text" wire:model="name" 
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400 dark:placeholder-gray-400 cursor-text">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button" wire:click="resetForm" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        {{ $editingPermissionId ? 'Actualizează' : 'Crează' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>