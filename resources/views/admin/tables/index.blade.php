<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Manajemen Meja') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Tambah Meja Baru</h3>
                        <form action="{{ route('admin.tables.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="number" :value="__('Nomor Meja')" class="text-xs font-bold uppercase" />
                                <x-text-input id="number" class="block mt-1 w-full" type="text" name="number" required placeholder="Contoh: 01" />
                                <x-input-error :messages="$errors->get('number')" class="mt-2" />
                            </div>
                            <x-primary-button class="w-full justify-center bg-indigo-600">
                                {{ __('Simpan Meja') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">No. Meja</th>
                                    <th class="px-6 py-4">Hash / QR Link</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($tables as $table)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <span class="text-lg font-black text-indigo-600 tracking-tighter">Meja {{ $table->number }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <code class="text-[10px] bg-gray-100 px-2 py-1 rounded text-gray-600">/scan/{{ $table->hash }}</code>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-3">
                                                <a href="{{ route('admin.tables.print', $table->id) }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-xs font-bold uppercase transition">Cetak QR</a>
                                                <span class="text-gray-300">|</span>
                                                <form action="{{ route('admin.tables.toggleStatus', $table->id) }}" method="POST" onsubmit="return confirm('Ubah status meja ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs font-bold uppercase tracking-widest {{ $table->status === 'available' ? 'text-gray-500 hover:text-gray-700' : 'text-green-600 hover:text-green-800' }}">
                                                        {{ $table->status === 'available' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                                <span class="text-gray-300">|</span>
                                                <form id="delete-form-{{ $table->id }}" action="{{ route('admin.tables.destroy', $table->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete('{{ $table->id }}')" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic text-sm">Belum ada meja.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Tangkap session('success') atau session('error') dari Flash Message Controller
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        // Fungsi intersep tombol hapus
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Meja?',
                text: "Data riwayat tidak bisa dikembalikan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // red-500
                cancelButtonColor: '#6b7280', // gray-500
                confirmButtonText: 'Ya, Konfirmasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jalankan form submit jika OK
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>