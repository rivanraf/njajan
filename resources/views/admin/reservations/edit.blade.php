<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Edit Reservasi') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-white">
                    
                    <div class="mb-6 pb-4 border-b border-gray-100">
                        <p class="text-sm text-gray-600">Edit data untuk reservasi <span class="font-bold text-indigo-600 uppercase tracking-widest">{{ $reservation->booking_code }}</span>.</p>
                    </div>

                    <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                                <input type="text" name="name" value="{{ $reservation->name }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- WhatsApp --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ $reservation->whatsapp }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- Meja --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pilih Meja</label>
                                <select name="table_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ $reservation->table_id == $table->id ? 'selected' : '' }}>
                                            Meja {{ $table->number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status Reservasi</label>
                                <select name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>MENUNGGU</option>
                                    <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>CHECK-IN</option>
                                    <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>BATAL</option>
                                </select>
                            </div>

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                <input type="date" name="reservation_date" value="{{ $reservation->reservation_date }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- Jam --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jam Kedatangan</label>
                                <input type="time" name="reservation_time" value="{{ $reservation->reservation_time }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.reservations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>