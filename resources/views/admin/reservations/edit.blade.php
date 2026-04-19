<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] p-10 border border-gray-100">
                
                <div class="mb-8">
                    <h2 class="text-2xl font-black tracking-tighter text-gray-900">Edit Reservasi</h2>
                    <p class="text-sm text-red-500 font-bold uppercase tracking-widest">{{ $reservation->booking_code }}</p>
                </div>

                <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nama Pelanggan</label>
                            <input type="text" name="name" value="{{ $reservation->name }}" class="w-full border-gray-200 rounded-2xl focus:ring-red-500 focus:border-red-500 font-bold">
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ $reservation->whatsapp }}" class="w-full border-gray-200 rounded-2xl focus:ring-red-500 focus:border-red-500 font-bold">
                        </div>

                        {{-- Meja --}}
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Pilih Meja</label>
                            <select name="table_id" class="w-full border-gray-200 rounded-2xl focus:ring-red-500 font-bold">
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" {{ $reservation->table_id == $table->id ? 'selected' : '' }}>
                                        Meja {{ $table->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Status Reservasi</label>
                            <select name="status" class="w-full border-gray-200 rounded-2xl focus:ring-red-500 font-bold">
                                <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>MENUNGGU</option>
                                <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>CHECK-IN</option>
                                <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>BATAL</option>
                            </select>
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal</label>
                            <input type="date" name="reservation_date" value="{{ $reservation->reservation_date }}" class="w-full border-gray-200 rounded-2xl focus:ring-red-500 font-bold">
                        </div>

                        {{-- Jam --}}
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jam Kedatangan</label>
                            <input type="time" name="reservation_time" value="{{ $reservation->reservation_time }}" class="w-full border-gray-200 rounded-2xl focus:ring-red-500 font-bold">
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end gap-4">
                        <a href="{{ route('admin.reservations.index') }}" class="px-6 py-3 text-sm font-bold text-gray-400 hover:text-gray-600 transition">Batal</a>
                        <button type="submit" class="px-10 py-3 bg-gray-900 text-white rounded-2xl font-black tracking-tighter hover:bg-red-600 transition-all shadow-lg shadow-gray-200">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>