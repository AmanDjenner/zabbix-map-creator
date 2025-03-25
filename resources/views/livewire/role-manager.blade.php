<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager de roluri</h1>

    
    @can('create roles')
        <button wire:click="$set('showModal', true)" 
                class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded mb-4">
            Adaugă rol
        </button>
    @endcan

    <div class="flex justify-center">
        <table class="w-[70%] bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">Nume</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Permisiuni</th>
                    <th class="w-[200] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td class="w-[247] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">{{ $role->name }}</td>
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $role->permissions->pluck('name')->implode(', ') }}</td>
                        <td class="w-[200] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-center">
                            @can('edit roles')
                                <button wire:click="editRole({{ $role->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete roles')
                                <button wire:click="deleteRole({{ $role->id }})" 
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
        <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700">
            <h2 class="text-xl mb-4 sticky top-0 bg-gray-100 dark:bg-zinc-900 z-10">{{ $editingRoleId ? 'Editează rol' : 'Crează rol' }}</h2>
            <form wire:submit.prevent="{{ $editingRoleId ? 'updateRole' : 'createRole' }}">
                <div class="mb-4">
                    <label class="block mb-1">Rolul</label>
                    <input type="text" wire:model="name" 
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400 dark:placeholder-gray-400 cursor-text">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Permisiuni</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($groupedPermissions as $group => $permissions)
                            <div class="mb-2">
                                @if ($editingGroup === $group)
                                    <div class="flex items-center">
                                        <input type="text" 
                                               wire:model.debounce.500ms="groupNames.{{ $group }}" 
                                               wire:keydown.enter="saveGroup('{{ $group }}')"
                                               wire:blur="saveGroup('{{ $group }}')"
                                               class="font-bold text-lg border p-1 rounded w-full dark:bg-zinc-800 dark:text-white"
                                               autofocus>
                                        @error("groupNames.{$group}") <span class="text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                @else
                                    <h3 wire:click="editGroup('{{ $group }}')" 
                                        class="font-bold text-lg cursor-pointer hover:text-zinc-500">
                                        {{ $groupNames[$group] ?? ucfirst($group) }}
                                    </h3>
                                @endif
                                @foreach ($permissions as $id => $name)
                                    <div>
                                        <input type="checkbox" 
                                               wire:model="selectedPermissions" 
                                               value="{{ $name }}"
                                               id="perm-{{ $id }}"
                                               class="text-zinc-500 dark:text-zinc-400">
                                        <label for="perm-{{ $id }}">{{ $name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    @error('selectedPermissions') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button" wire:click="resetForm" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        {{ $editingRoleId ? 'Actualizează' : 'Crează' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>