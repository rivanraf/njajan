<x-layout title="Njajan++ | Home">        
        <!-- Notifikasi Success -->
        @if(session('success'))
    <div id="success-alert" 
        class="fixed top-24 left-1/2 -translate-x-1/2 z-[70] 
               flex items-center gap-2 
               /* RESPONSIVE WIDTH & PADDING */
               w-[max-content] max-w-[90vw] 
               px-[clamp(0.75rem,3vw,1.25rem)] py-[clamp(0.5rem,2vw,0.75rem)] 
               /* VISUAL STYLE */
               bg-white shadow-md border border-gray-100 
               rounded-[clamp(0.75rem,2vw,1rem)] 
               /* ANIMATION BASE */
               transition-all duration-500 transform scale-100 opacity-100">
        
        <div class="bg-green-400 p-0.5 rounded-full text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>

        <span class="text-xs font-semibold text-gray-900 leading-none">
            {{ session('success') }}
        </span>
    </div>
@endif

        <!-- HEADER -->
        @php
            $tableHash = session('table_hash') ?? request()->cookie('table_hash');
            $backUrl = $tableHash ? route('scan.qr', $tableHash) : 'javascript:history.back()';
        @endphp
        <x-navbar title="Details" showBack="true" backUrl="{{ $backUrl }}" showCart="true" />

        <form id="addToCartForm" action="{{ route('add-to-cart', $menu->id) }}" method="POST" class="flex flex-col flex-1 relative">
            @csrf

            <!-- FULL SCROLLABLE CONTENT WRAPPER -->
            <div class="overflow-y-auto scroll-smooth no-scrollbar w-full" style="-webkit-overflow-scrolling: touch;">
                <div class="min-h-full pb-[120px]">
                <!-- IMAGE SECTION MATCHING MOCKUP -->
            <div class="w-full bg-gray-50 aspect-video flex items-center justify-center relative overflow-hidden border-b border-gray-100">
                @if($menu->image)
                    <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover">
                @else
                    <div class="flex flex-col items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">No Image Available</span>
                    </div>
                @endif
            </div>

            <!-- VERTICAL STACK CONTAINER -->
            <div class="flex flex-col px-4 py-3">
                <!-- TITLE & PRICE -->
                <div class="flex items-baseline justify-between gap-4 mb-4">
                    {{-- Nama Menu (Sisi Kiri) --}}
                    <h1 class="font-sans font-semibold text-base md:text-base text-gray-900 tracking-tight">
                        {{ $menu->name }}
                    </h1>

                    {{-- Harga (Sisi Kanan) --}}
                    <span class="font-sans font-medium text-gray-900 text-base whitespace-nowrap">
                        Rp{{ number_format($menu->price, 0, '.', '.') }}
                    </span>
                </div>

                <!-- DESKRIPSI -->
                <div class="mb-4">
                    {{-- Container utama menggunakan inline agar tombol bisa menyambung teks --}}
                    <div id="descContainer" class="font-sans font-normal text-sm text-gray-600 leading-relaxed overflow-hidden">
                        <span id="descText" class="line-clamp-3">
                            {{ $menu->description ?? 'Deskripsi belum tersedia' }}
                        </span>
                        
                        @if(isset($menu->description) && strlen($menu->description) > 100)
                            <button onclick="toggleDescription(this)" 
                                    id="readMoreBtn" 
                                    class="text-[#FF4647] text-xs font-semibold focus:outline-none">
                                Read More
                            </button>
                        @endif
                    </div>
                </div>

                <script>
                function toggleDescription(btn) {
                    const textSpan = document.getElementById('descText');
                    
                    if (textSpan.classList.contains('line-clamp-3')) {
                        // Mode: Expand (Lihat Semua)
                        textSpan.classList.remove('line-clamp-3');
                        btn.textContent = ' Read Less';
                        btn.classList.add('block', 'mt-1'); // Pindah ke baris baru saat panjang agar rapi
                    } else {
                        // Mode: Clamp (Potong 3 Baris)
                        textSpan.classList.add('line-clamp-3');
                        btn.textContent = 'Read More';
                        btn.classList.remove('block', 'mt-1');
                    }
                }
                </script>

                <!-- VARIAN OPSI -->
                @if($menu->type)
                    <div class="mb-4">
                        <div class="mb-4">
                        <h2 class="font-sans font-semibold text-base md:text-base text-gray-900 mb-2 capitalize block">
                            Variant
                        </h2>
                        <span class="font-sans font-normal text-xs capitalize text-gray-600 block -mt-0.5">
                            Must choose one variant
                        </span>
                        </div>
                        <div id="variant-section" class="flex flex-wrap gap-3 transition-all duration-300 border-2 border-transparent rounded-2xl p-2 -m-2">
                            {{-- Input Hidden untuk menyimpan data ke backend --}}
                            <input type="hidden" name="variant" id="variantInput" value="">
                            
                            @foreach(explode(',', $menu->type) as $variant)
                                @php $variantName = trim($variant); @endphp
                                <x-category-item name="{{ $variantName }}" data-variant-value="{{ $variantName }}" :active="false" {{-- Default false, akan diubah oleh JS --}} onclick="selectVariant('{{ $variantName }}', this)" class="border-[1.5px]" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- CATATAN TEXTAREA -->
                <div class="mb-4">
                    <div class="mb-4">
                        <h2 class="font-sans font-semibold text-base md:text-base text-gray-900 mb-2 capitalize block">
                            Notes
                        </h2>
                        <span class="font-sans font-normal text-xs capitalize text-gray-600 block -mt-0.5">
                            Optional
                        </span>
                    </div>
                    <textarea name="notes" rows="4" class="w-full bg-white border border-gray-400 rounded-lg p-4 text-[clamp(0.75rem,2vw,0.813rem)] font-normal focus:ring-1 focus:ring-gray-900 focus:border-gray-900 outline-none transition placeholder:text-gray-600 shadow-sm" placeholder="Example: Kasirnya ganteng!"></textarea>
                </div>
                </div>
                </div>
            </div>

            <!-- STICKY BOTTOM BAR -->
            <x-bottom-bar>
    <div class="flex justify-between items-center px-1">
        {{-- Harga Total: Menggunakan variant price (Level 3: text-base font-bold) --}}
        <span id="bottomPrice" class="font-sans font-semibold text-gray-900 text-base">
            Rp{{ number_format($menu->price, 0, '.', '.') }}
        </span>
        
        <div class="flex items-center gap-3">
            {{-- Tombol Minus --}}
            <button type="button" onclick="updateQty(-1)" class="w-7 h-7 flex items-center justify-center rounded-full border border-gray-300 text-gray-800 hover:bg-gray-50 transition active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
            </button>
            
            {{-- Input Qty: Menggunakan variant body dengan penambahan font-semibold (Level 4) --}}
            <span id="displayQty" class="font-sans font-semibold text-base md:text-lg text-gray-700 w-4 text-center block">
                1
            </span>
            <input type="hidden" name="qty" id="inputQty" value="1">
            
            {{-- Tombol Plus --}}
            <button type="button" onclick="updateQty(1)" class="w-7 h-7 flex items-center justify-center rounded-full bg-transparent border-[1.5px] border-[#FF4647] text-[#FF4647] hover:bg-[#FF4647]/5 active:scale-90 transition shadow-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Tombol Utama: Menggunakan variant h3 dengan class text-base font-semibold --}}
    <x-button type="submit" variant="primary" class="w-full text-base font-semibold tracking-tight">
        Add to Cart
    </x-button>
</x-bottom-bar>
        </form>

    <!-- DYNAMIC PRICE SCRIPT -->
    <script>
        const basePrice = {{ $menu->price }};
        let currentQty = 1;

        function updateQty(change) {
            currentQty += change;
            if (currentQty < 1) currentQty = 1;
            
            document.getElementById('displayQty').innerText = currentQty;
            document.getElementById('inputQty').value = currentQty;
            
            const formatter = new Intl.NumberFormat('id-ID');
            const total = formatter.format(basePrice * currentQty);
            
            document.getElementById('bottomPrice').innerText = 'Rp' + total;
            document.getElementById('btnPrice').innerText = 'Rp' + total;
        }
    </script>
    
    <!-- AUTO-HIDE ALERT SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertEl = document.getElementById('success-alert');
            if(alertEl) {
                setTimeout(() => {
                    alertEl.style.opacity = '0';
                    setTimeout(() => alertEl.style.display = 'none', 500); 
                }, 3000);
            }
        });

        function openZoom() {
    const modal = document.getElementById('zoomModal');
    const zoomedImg = document.getElementById('zoomedImg');
    const originalImg = document.getElementById('menuImage');

    if (!originalImg) return; // Jika tidak ada gambar, batalkan

    zoomedImg.src = originalImg.src;
    modal.classList.remove('hidden');
    
    // Animasi masuk agar smooth
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        zoomedImg.classList.remove('scale-90');
        zoomedImg.classList.add('scale-100');
    }, 10);
}

function closeZoom() {
    const modal = document.getElementById('zoomModal');
    const zoomedImg = document.getElementById('zoomedImg');

    modal.classList.add('opacity-0');
    zoomedImg.classList.remove('scale-100');
    zoomedImg.classList.add('scale-90');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function selectVariant(value, btnEl) {
    // 1. Simpan nilai ke input hidden
    const input = document.getElementById('variantInput');
    if(input) input.value = value;
    
    // 2. Definisi Class Outlined Merah
    const activeClasses = [
        'bg-transparent',
        'border-[#FF4647]',
        'text-[#FF4647]',
        'font-semibold'
    ];
    
    const inactiveClasses = [
        'bg-white', 
        'border-gray-200', 
        'text-gray-500', 
        'font-medium'
    ];

    // Class hover
    const hoverClasses = ['hover:bg-gray-50'];
    
    // 3. Reset Semua Tombol
    const variantBtns = document.querySelectorAll('[data-variant-value]');
    variantBtns.forEach(btn => {
        // Hapus class aktif sebelumnya
        btn.classList.remove(
            ...activeClasses, 
            'border-black/10',
            'shadow-[inset_0_1px_1px_0_rgba(255,255,255,0.3)]',
            'shadow-[inset_0_-1px_1px_0_rgba(0,0,0,0.2)]',
            'drop-shadow-md',
            'bg-[#5D1525]',
            'text-white',
            'border-[#5D1525]'
        );
        
        btn.classList.add(...inactiveClasses);
        btn.classList.add(...hoverClasses);
        
        if(!btn.classList.contains('transition-all')) {
            btn.classList.add('transition-all', 'duration-200');
        }
    });
    
    // 4. Set Tombol Aktif & Matikan Hover
    btnEl.classList.remove(...inactiveClasses);
    btnEl.classList.remove(...hoverClasses);
    btnEl.classList.add(...activeClasses);

    // 5. Hilangkan Peringatan (Jika Ada)
    const variantSection = document.getElementById('variant-section');
    if (variantSection) {
        variantSection.classList.remove('border-[#FF4647]');
        const msg = document.getElementById('variant-error-msg');
        if (msg) msg.remove();
    }
}

// Event Listener untuk Validasi Form
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addToCartForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            const variantInput = document.getElementById('variantInput');
            
            // Cek apakah menu ini memiliki opsi varian (input variant ada di DOM)
            if (variantInput) {
                // Cek jika valuenya kosong
                if (!variantInput.value.trim()) {
                    event.preventDefault(); // Hentikan submit
                    
                    const variantSection = document.getElementById('variant-section');
                    if (variantSection) {
                        // Scroll halus ke arah varian
                        variantSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Berikan border merah
                        variantSection.classList.add('border-[#FF4647]');
                        
                        // Tambahkan teks peringatan jika belum ada
                        if (!document.getElementById('variant-error-msg')) {
                            const errorMsg = document.createElement('span');
                            errorMsg.id = 'variant-error-msg';
                            errorMsg.className = 'text-[#FF4647] text-xs font-medium mt-2 block w-full pl-1';
                            errorMsg.innerText = '*Please select one of the variants first.';
                            variantSection.appendChild(errorMsg);
                        }
                    }
                }
            }
        });
    }
});
    </script>
</x-layout>