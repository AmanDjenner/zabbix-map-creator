<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager Categorii Leziuni</h1>

    <div class="mb-4">
        @can('create categories')
            <button wire:click="$set('showModal', true)" 
                    class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded">
                Adaugă categorie
            </button>
        @endcan
    </div>

    <div class="flex justify-center">
        <table class="w-[70%] bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Nume</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{{ $category->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700">
                            @can('edit categories')
                                <button wire:click="editCategory({{ $category->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete categories')
                                <button wire:click="deleteCategory({{ $category->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="py-2 px-4 text-center text-gray-900 dark:text-white">Nicio categorie găsită.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
            <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-1/2 text-gray-900 dark:text-white border border-gray-300 dark:border-zinc-700">
                <h2 class="text-xl mb-4">{{ $editingCategoryId ? 'Editează categorie' : 'Crează categorie' }}</h2>
                <form wire:submit.prevent="{{ $editingCategoryId ? 'updateCategory' : 'createCategory' }}">
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
                            {{ $editingCategoryId ? 'Actualizează' : 'Crează' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>