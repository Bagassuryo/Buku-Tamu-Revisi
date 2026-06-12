<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu - Diskominfo Kabupaten Gresik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .toast-enter {
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s;
        }

        .toast-show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-hide {
            transform: translateX(120%) !important;
            opacity: 0 !important;
            transition: transform 0.3s ease-in, opacity 0.25s !important;
        }

        @keyframes progress-shrink {
            from {
                transform: scaleX(1);
            }

            to {
                transform: scaleX(0);
            }
        }

        .toast-progress {
            animation: progress-shrink linear forwards;
            transform-origin: left;
        }

        .dd-item mark {
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 2px;
            padding: 0 1px;
        }

        #layanan-dropdown::-webkit-scrollbar {
            width: 4px;
        }

        #layanan-dropdown::-webkit-scrollbar-track {
            background: transparent;
        }

        #layanan-dropdown::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* ── Kamera overlay ── */
        #kamera-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
        }

        #kamera-overlay.aktif {
            display: flex;
        }

        #video-preview {
            width: 320px;
            height: 240px;
            border-radius: 16px;
            object-fit: cover;
            border: 3px solid white;
            transform: scaleX(-1);
        }

        #countdown-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 72px;
            font-weight: 900;
            color: white;
            text-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
            pointer-events: none;
        }

        #foto-preview-wrap {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        #foto-result {
            width: 120px;
            height: 90px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #1B75BC;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen font-sans ">

    {{-- Toast Container --}}
    <div id="bt-toast-container" class="fixed top-5 right-5 z-9999 flex flex-col gap-2.5 pointer-events-none"></div>

    {{-- Kamera Overlay --}}
    <div id="kamera-overlay">
        <p class="text-white text-sm font-semibold tracking-wide opacity-80">Mengambil foto tamu…</p>
        <div class="relative">
            <video id="video-preview" autoplay playsinline muted></video>
            <div id="countdown-ring"></div>
        </div>
        <p class="text-white/60 text-xs">Harap hadap kamera</p>
    </div>

    {{-- Canvas tersembunyi untuk capture --}}
    <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>

    {{-- Navbar --}}
    @include('layouts.nav')

    {{-- Hero --}}
    <div class="bg-linear-to-br from-[#1a2a6c] to-[#1B75BC] pt-10 pb-20 text-center relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-52 h-52 bg-white/4 rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-12 w-72 h-72 bg-white/3 rounded-full pointer-events-none"></div>
        <h1 class="font-serif text-[32px] text-white mb-2 leading-tight">Buku Tamu</h1>
        <p class="text-white/70 text-sm">Silakan lengkapi data kunjungan Anda dengan benar dan jelas</p>
    </div>

    {{-- Card Form --}}
    <div class="max-w-4xl mx-auto px-5 -mt-10 pb-12 relative z-10">
        <div class="bg-white rounded-2xl shadow-2xl overflow-visible">
            <div class="p-7">

                {{-- Form --}}
                <form action="{{ route('tamu.store') }}" method="POST" class="space-y-5" id="formTamu">
                    @csrf

                    {{-- Hidden input foto base64 --}}
                    <input type="hidden" name="foto" id="foto-input">
                    {{-- Instansi yang Dituju --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                            Instansi yang Dituju <span class="text-red-500">*</span>
                        </label>
                    </div>
                    @if (Auth::user()->role !== 'super_admin')
                        {{-- Admin biasa: instansi otomatis, tidak bisa diubah --}}
                        <input type="hidden" name="instansi_id" value="{{ $instansi->id }}">
                        <div
                            class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-[#1a2a6c] px-3 py-2.5 rounded-xl">
                            <span class="text-sm font-semibold">{{ $instansi->nama }}</span>
                            <i class="ti ti-lock text-xs text-slate-400 ml-auto"></i>
                        </div>
                        <p class="text-[11.5px] text-slate-400 mt-1.5">Instansi ditentukan otomatis sesuai akun Anda
                        </p>
                        {{-- Layanan (muncul setelah Instansi dipilih) --}}
                        <div id="layanan-wrap" class="hidden">
                            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                                <i class="ti ti-list-details text-[15px] text-[#1B75BC]"></i>
                                Jenis Layanan <span class="text-red-500">*</span>
                            </label>
                            <select name="layanan_id" id="layanan-select"
                                class="w-full px-3.5 py-2.75 border-[1.5px] border-slate-200 rounded-xl text-sm text-slate-800 outline-none transition
               focus:border-[#1B75BC] focus:ring-2 focus:ring-blue-100 bg-white cursor-pointer appearance-none">
                                <option>Pilih Jenis Layanan</option>
                            </select>
                            <p class="text-[11.5px] text-slate-400 mt-1.5">Pilih layanan spesifik yang Anda tuju</p>
                        </div>

                        <hr class="border-slate-300">

                        {{-- Nama --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                                <i class="ti ti-user text-[15px] text-[#1B75BC]"></i>
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_tamu" autocomplete="off" value="{{ old('nama_tamu') }}"
                                placeholder="Masukkan nama lengkap sesuai identitas"
                                class="w-full px-3.5 py-2.75 border-[1.5px] rounded-xl text-sm text-slate-800 placeholder-slate-400 outline-none transition
                                focus:border-[#1B75BC] focus:ring-2 focus:ring-blue-100
                                {{ $errors->has('nama_tamu') ? 'border-red-400 ring-2 ring-red-100' : 'border-slate-200' }}">
                            @error('nama_tamu')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Row: Instansi + No HP --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Instansi --}}
                            <div>
                                <label
                                    class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                                    <i class="ti ti-building text-[15px] text-[#1B75BC]"></i>
                                    Asal Instansi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="asal_instansi" autocomplete="off"
                                    value="{{ old('asal_instansi') }}" placeholder="Contoh: Dinas Pendidikan"
                                    maxlength="50"
                                    class="w-full px-3.5 py-2.75 border-[1.5px] rounded-xl text-sm text-slate-800 placeholder-slate-400 outline-none transition
                                    focus:border-[#1B75BC] focus:ring-2 focus:ring-blue-100
                                    {{ $errors->has('asal_instansi') ? 'border-red-400 ring-2 ring-red-100' : 'border-slate-200' }}">
                                @error('asal_instansi')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- No HP --}}
                            <div>
                                <label
                                    class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                                    <i class="ti ti-phone text-[15px] text-[#1B75BC]"></i>
                                    Nomor HP / WA <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="no_hp" id="f-nohp" autocomplete="off"
                                    value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" maxlength="15"
                                    class="w-full px-3.5 py-2.75 border-[1.5px] rounded-xl text-sm text-slate-800 placeholder-slate-400 outline-none transition
                                    focus:border-[#1B75BC] focus:ring-2 focus:ring-blue-100
                                    {{ $errors->has('no_hp') ? 'border-red-400 ring-2 ring-red-100' : 'border-slate-200' }}">
                                @error('no_hp')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>


                        {{-- Keterangan --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                                <i class="ti ti-notes text-[15px] text-[#1B75BC]"></i>
                                Keperluan Kunjungan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="keterangan" id="f-keterangan" autocomplete="off" rows="4" maxlength="300"
                                placeholder="Tuliskan keperluan atau maksud kunjungan Anda secara singkat..."
                                class="w-full px-3.5 py-2.75 border-[1.5px] rounded-xl text-sm text-slate-800 placeholder-slate-400 outline-none transition resize-none
                                focus:border-[#1B75BC] focus:ring-2 focus:ring-blue-100
                                {{ $errors->has('keterangan') ? 'border-red-400 ring-2 ring-red-100' : 'border-slate-200' }}">{{ old('keterangan') }}</textarea>
                            <div class="flex justify-end mt-1">
                                <span id="char-count" class="text-[11px] text-slate-400">
                                    {{ strlen(old('keterangan', '')) }} / 300
                                </span>
                            </div>
                            @error('keterangan')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Foto preview (muncul setelah foto diambil) --}}
                        <div id="foto-preview-wrap" class="flex flex-col items-center gap-2">
                            <p class="text-[12px] text-slate-500 font-semibold flex items-center gap-1">
                                <i class="ti ti-camera-check text-green-500"></i>
                                Foto tamu berhasil diambil
                            </p>
                            <img src="" alt="Foto Preview" id="foto-result">
                        </div>

                        {{-- Submit --}}
                        <button type="submit" id="btn-submit"
                            class="w-full mt-2 py-3.5 bg-linear-to-r from-[#1a2a6c] to-[#1B75BC] text-white font-bold text-[15px] rounded-xl
                            flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.99] transition cursor-pointer tracking-wide">
                            <i class="ti ti-send text-[17px]"></i>
                            Kirim Data Kunjungan
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showToast('success', 'Berhasil!', '{{ session('success') }}', 3000);
            @endif

            @if ($errors->any())
                showToast('error', 'Data Tidak Valid', 'Mohon periksa kembali isian form di bawah ini.', 4000);
            @endif

            const instansiData = {!! $instansiJson !!};
            const layananWrap = document.getElementById('layanan-wrap');
            const layananSelect = document.getElementById('layanan-select');
            const selectedLayanan = '{{ old('layanan_id') }}';

            function resetLayanan() {
                layananSelect.innerHTML = '<option value="">Pilih Jenis Layanan</option>';
                layananWrap.classList.add('hidden');
            }

            function renderLayanan(instansiId) {
                const instansi = instansiData.find(i => i.id == instansiId);
                resetLayanan();

                if (!instansi || !instansi.layanan || instansi.layanan.length === 0) {
                    return;
                }

                instansi.layanan.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama;
                    if (item.id == selectedLayanan) {
                        option.selected = true;
                    }
                    layananSelect.appendChild(option);
                });

                layananWrap.classList.remove('hidden');
            }

            @if (Auth::user()->role !== 'super_admin')
                const instansiValue = '{{ $instansi->id ?? '' }}';
                if (instansiValue) {
                    renderLayanan(instansiValue);
                }
            @endif
        });
    </script>

    @include('layouts.footer')
</body>

</html>
