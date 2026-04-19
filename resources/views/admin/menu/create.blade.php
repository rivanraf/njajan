<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Tambah Menu Baru') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Menu')" class="text-xs font-bold uppercase" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="category_id" :value="__('Kategori')" class="text-xs font-bold uppercase" />
                            <select name="category_id" id="category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="price" :value="__('Harga (Rp)')" class="text-xs font-bold uppercase" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Deskripsi')" class="text-xs font-bold uppercase" />
                        <textarea name="description" id="description" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Pilihan Varian (Pisahkan dengan koma)')" class="text-xs font-bold uppercase" />
                        <x-text-input id="type" class="block mt-1 w-full" type="text" name="type" 
                            :value="old('type', $menu->type ?? '')" 
                            placeholder="Contoh: Hot, Ice atau Pedas, Sedang, Biasa" />
                        <p class="text-[10px] text-gray-400 mt-1 italic">*Kosongkan jika tidak ada varian.</p>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="image" :value="__('Foto Menu')" class="text-xs font-bold uppercase" />
                        <input type="file" name="image" id="image" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.menu.index') }}" class="text-xs font-bold text-gray-500 uppercase mr-4 tracking-widest hover:text-gray-700 transition">Batal</a>
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 shadow-md transition">
                            {{ __('Simpan Menu') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>