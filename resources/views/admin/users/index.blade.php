<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Manajemen Karyawan (Kasir)') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Daftarkan Akun Baru</h3>
                        
                        @if(session('success'))
                            <div class="mb-4 text-sm text-green-600 font-bold italic">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.users.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-[10px] font-bold uppercase" />
                                    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full text-sm" required />
                                </div>
                                <div>
                                    <x-input-label for="email" :value="__('Email Login')" class="text-[10px] font-bold uppercase" />
                                    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full text-sm" required />
                                </div>
                                <div>
                                    <x-input-label for="role" :value="__('Role')" class="text-[10px] font-bold uppercase" />
                                    <select name="role" id="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">
                                        <option value="kasir">Kasir</option>
                                        <option value="admin">Admin (Owner)</option>
                                    </select>
                                </div>

                                <div class="relative">
                                    <x-input-label for="password" :value="__('Password')" class="text-[10px] font-bold uppercase" />
                                    <div class="relative mt-1">
                                        <x-text-input id="password" name="password" type="password" class="block w-full text-sm pr-10" required />
                                        
                                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 focus:outline-none">
                                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <x-primary-button class="w-full justify-center bg-indigo-600 py-3">
                                    {{ __('Buat Akun') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">Nama & Email</th>
                                    <th class="px-6 py-4">Role</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded text-[10px] font-black uppercase {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akses karyawan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold uppercase text-[10px]">Cabut Akses</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
            }
        }
    </script>
</x-app-layout>