<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager de categorii și subcategorii evenimente</h1>

    <!-- Butoane pentru creare -->
    <div class="mb-4">
        @can('create categories')
            <button wire:click="$set('showCategoryModal', true)" 
                    class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded mr-2">
                Adaugă categorie
            </button>
            <button wire:click="$set('showSubcategoryModal', true)" 
                    class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded">
                Adaugă subcategorie
            </button>
        @endcan
    </div>

    <!-- Tabel cu categorii și subcategorii -->
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
                    @foreach ($category->subcategories as $subcategory)
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white pl-8">↳ {{ $subcategory->name }}</td>
                            <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700">
                                @can('edit categories')
                                    <button wire:click="editSubcategory({{ $subcategory->id }})" 
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                                @endcan
                                @can('delete categories')
                                    <button wire:click="deleteSubcategory({{ $subcategory->id }})" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="2" class="py-2 px-4 text-center text-gray-900 dark:text-white">Nicio categorie găsită.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal pentru creare/editare categorie -->
    @if ($showCategoryModal)
    <div class="fixed top-0 bottom-0 left-0 right-0 lg:left-[16rem] bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
                <h2 class="text-xl mb-4">{{ $editingCategoryId ? 'Editează categorie' : 'Crează categorie' }}</h2>
                <form wire:submit.prevent="{{ $editingCategoryId ? 'updateCategory' : 'createCategory' }}">
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
                            {{ $editingCategoryId ? 'Actualizează' : 'Crează' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal pentru creare/editare subcategorie -->
    @if ($showSubcategoryModal)
    <div class="fixed top-0 bottom-0 left-0 right-0 lg:left-[16rem] bg-zinc-800 bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
                <h2 class="text-xl mb-4">{{ $editingSubcategoryId ? 'Editează subcategorie' : 'Crează subcategorie' }}</h2>
                <form wire:submit.prevent="{{ $editingSubcategoryId ? 'updateSubcategory' : 'createSubcategory' }}">
                    <div class="mb-4">
                        <label class="block mb-1">Nume</label>
                        <input type="text" wire:model="name" 
                               class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400 dark:placeholder-gray-400 cursor-text">
                        @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1">Categorie părinte</label>
                        <select wire:model="categoryId" 
                                class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400">
                            <option value="">Selectează o categorie</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('categoryId') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="button" wire:click="resetForm" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            {{ $editingSubcategoryId ? 'Actualizează' : 'Crează' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>