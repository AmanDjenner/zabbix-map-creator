<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager de instituții</h1>

    <!-- Buton pentru a deschide modalul de creare -->
    @can('create institutions')
        <button wire:click="$set('showModal', true)" 
                class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded mb-4">
            Adaugă instituție
        </button>
    @endcan

    <!-- Tabel cu instituții -->
    <div class="flex justify-center">
        <table class="w-[70%] bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Nume</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($institutions as $institution)
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{{ $institution->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700">
                            @can('edit institutions')
                                <button wire:click="editInstitution({{ $institution->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete institutions')
                                <button wire:click="deleteInstitution({{ $institution->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal pentru creare/editare -->
    @if ($showModal)
    <div class="fixed inset-0 bg-zinc-800 bg-opacity-75 flex justify-center items-center">
        <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-1/2 text-gray-900 dark:text-white border border-gray-300 dark:border-zinc-700">
            <h2 class="text-xl mb-4">{{ $editingInstitutionId ? 'Editează instituție' : 'Crează instituție' }}</h2>
            <form wire:submit.prevent="{{ $editingInstitutionId ? 'updateInstitution' : 'createInstitution' }}">
            <div class="mb-4">
                    <label class="block mb-1">Nume</label>
                    <input type="text" wire:model="name" 
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400 dark:placeholder-gray-400 cursor-text">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button" wire:click="resetForm" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        {{ $editingInstitutionId ? 'Actualizează' : 'Crează' }}
                    </button>
                </div>
                </form>
            </div>
        </div>
    @endif
</div>