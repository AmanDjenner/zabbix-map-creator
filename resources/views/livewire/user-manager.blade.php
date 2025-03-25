<div>
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manager de utilizatori</h1>

    @can('create users')
        <button wire:click="$set('showModal', true)" 
                class="bg-blue-500 hover:bg-zinc-600 text-white px-4 py-2 rounded mb-4">
            Adaugă utilizator
        </button>
    @endcan

    
    <div class="flex justify-center">
        <table class="w-full bg-gray-100 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700">
            <thead>
                <tr>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Nume</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Rol</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Email</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Email Verificat</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Instituție</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Creat</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Actualizat</th>
                    <th class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-left">{{ $user->name }}</td>
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-centertext-center" >{{ $user->roles->first()->name ?? 'Fără rol' }}</td>
                        <td class="py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $user->email }}</td>
                        
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $user->email_verified_at ? $user->email_verified_at->format('d-m-Y H:i') : 'Ne-verificat' }}</td>
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $user->institution ? $user->institution->name : 'Fără instituție' }}</td>
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $user->created_at->format('d-m-Y H:i') }}</td>
                        <td class="w-[147] py-2 px-4 border-b border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-white text-center">{{ $user->updated_at->format('d-m-Y H:i') }}</td>
                        <td class="w-[200] py-2 px-4 border-b border-gray-300 dark:border-zinc-700">
                            @can('edit users')
                                <button wire:click="editUser({{ $user->id }})" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">Editează</button>
                            @endcan
                            @can('delete users')
                                <button wire:click="deleteUser({{ $user->id }})" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">Șterge</button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

   
    @if ($showModal)
    <div class="fixed inset-0 bg-zinc-800 bg-opacity-75 flex items-start justify-center overflow-y-auto p-4 lg:pl-[calc(16rem+1rem)]">
        <div class="bg-gray-100 dark:bg-zinc-900 p-6  w-full max-w-[calc(100vw-16rem-2rem)] lg:max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-300 dark:border-zinc-700 mt-4">
            <h2 class="text-xl mb-4 sticky top-0 bg-gray-100 dark:bg-zinc-900 z-10">{{ $editingUserId ? 'Editează utilizator' : 'Crează utilizator' }}</h2>
            <form wire:submit.prevent="{{ $editingUserId ? 'updateUser' : 'createUser' }}">
                <div class="mb-4">
                    <label class="block mb-1">Nume</label>
                    <input type="text" wire:model="name" 
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400 dark:placeholder-gray-400 cursor-text">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Email</label>
                    <input type="email" wire:model="email" 
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400 dark:placeholder-gray-400 cursor-text">
                    @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Parolă {{ $editingUserId ? '(lasă gol pentru a păstra parola existentă)' : '' }}</label>
                    <input type="password" wire:model="password" 
                           class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400 dark:placeholder-gray-400 cursor-text">
                    @error('password') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Instituție</label>
                    <select wire:model="selectedInstitution" 
                            class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400">
                        <option value="">Fără instituție</option>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedInstitution') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block mb-1">Rol</label>
                    <select wire:model="selectedRole" 
                            class="w-full border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-800 text-gray-900 dark:text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400">
                        <option value="">Selectează un rol</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedRole') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button" wire:click="resetForm" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded mr-2">Anulează</button>
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        {{ $editingUserId ? 'Actualizează' : 'Crează' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>