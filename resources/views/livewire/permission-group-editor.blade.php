<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Editor Grupuri Permisiuni</h1>

    <div class="flex justify-center">
        <table class="w-[70%] bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Nume Permisiune</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Grup</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">{{ $permission->name }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white">
                            @if ($editingPermissionId === $permission->id)
                                <input type="text" wire:model="group" class="border p-1 rounded w-full dark:bg-zinc-800 dark:text-white">
                            @else
                                {{ $permission->group ?? 'Fără grup' }}
                            @endif
                        </td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700">
                            @if ($editingPermissionId === $permission->id)
                                <button wire:click="updateGroup" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">Salvează</button>
                                <button wire:click="resetForm" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Anulează</button>
                            @else
                                <button wire:click="editPermission({{ $permission->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>