// resources/views/livewire/detinuti-crud.blade.php
<div class="container mx-auto p-4">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4">
        <button wire:click="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Adaugă Detinuti

        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-4">Data</th>
                    <th class="py-2 px-4">Institutia</th>
                    <th class="py-2 px-4">Total</th>
                    <th class="py-2 px-4">Deținuți reali</th>
                    <th class="py-2 px-4">Actiuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detinuti as $item)
                <tr class="border-b">
                    <td class="py-2 px-4">{{ $item->data }}</td>
                    <td class="py-2 px-4">{{ $item->institution->name }}</td>
                    <td class="py-2 px-4">{{ $item->total }}</td>
                    <td class="py-2 px-4">{{ $item->real_inmates }}</td>
                    <td class="py-2 px-4">
                        <button wire:click="edit({{ $item->id }})" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded">
                            Edit
                        </button>
                        <button wire:click="delete({{ $item->id }})" class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded ml-2">
                            Delete
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($isOpen)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <form wire:submit.prevent="{{ $detinutiId ? 'update' : 'store' }}">
                <div class="mb-4">
                    <label class="block text-gray-700">Date</label>
                    <input type="date" wire:model="data" class="w-full border rounded p-2">
                    @error('data') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Institution</label>
                    <select wire:model="id_institution" class="w-full border rounded p-2">
                        <option value="">Select Institution</option>
                        @foreach($institutions as $institution)
                            <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                        @endforeach
                    </select>
                    @error('id_institution') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Total</label>
                    <input type="number" wire:model="total" class="w-full border rounded p-2">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Real Inmates</label>
                    <input type="number" wire:model="real_inmates" class="w-full border rounded p-2">
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded mr-2">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                        {{ $detinutiId ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>