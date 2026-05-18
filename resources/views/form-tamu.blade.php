<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu - Diskominfo Kabupaten Gresik</title>
    @vite('resources/css/app.css')
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

<body class="bg-slate-100 min-h-screen font-sans">

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

                {{-- Toast success dari session Laravel --}}
                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('success', 'Berhasil!', '{{ session('success') }}', 3000);
                        });
                    </script>
                @endif

                {{-- Form --}}
                <form action="{{ route('tamu.store') }}" method="POST" class="space-y-5" id="formTamu">
                    @csrf

                    {{-- Hidden input foto base64 --}}
                    <input type="hidden" name="foto" id="foto-input">

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
                            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                                <i class="ti ti-building text-[15px] text-[#1B75BC]"></i>
                                Asal Instansi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="asal_instansi" autocomplete="off"
                                value="{{ old('asal_instansi') }}" placeholder="Contoh: Dinas Pendidikan"
                                class="w-full px-3.5 py-2.75 border-[1.5px] rounded-xl text-sm text-slate-800 placeholder-slate-400 outline-none transition
                                    focus:border-[#1B75BC] focus:ring-2 focus:ring-blue-100
                                    {{ $errors->has('asal_instansi') ? 'border-red-400 ring-2 ring-red-100' : 'border-slate-200' }}">
                            @error('asal_instansi')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
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

                    {{-- Layanan Search --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-[13px] font-semibold text-slate-700 mb-1.5">
                            <i class="ti ti-search text-[15px] text-[#1B75BC]"></i>
                            Layanan / OPD yang Dituju <span class="text-red-500">*</span>
                        </label>

                        <input type="hidden" name="layanan" id="layanan-value" value="{{ old('layanan') }}">

                        <div class="relative" id="search-wrap">
                            <div class="relative flex items-center">
                                <i
                                    class="ti ti-search absolute left-3 text-slate-400 text-base pointer-events-none z-10"></i>
                                <input type="text" id="layanan-search" autocomplete="off"
                                    placeholder="Ketik nama OPD atau layanan..."
                                    class="w-full pl-9 pr-9 py-2.75 border-[1.5px] rounded-xl text-sm text-slate-800 placeholder-slate-400 outline-none transition
                                        focus:border-[#1B75BC] focus:ring-2 focus:ring-blue-100
                                        {{ $errors->has('layanan') ? 'border-red-400 ring-2 ring-red-100' : 'border-slate-200' }}"
                                    value="{{ old('layanan') }}">
                                <button type="button" id="layanan-clear"
                                    class="absolute right-2.5 hidden items-center justify-center w-5 h-5 rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                                    <i class="ti ti-x text-xs"></i>
                                </button>
                            </div>

                            <div id="layanan-dropdown"
                                class="hidden absolute top-[calc(100%+4px)] left-0 right-0 bg-white border-[1.5px] border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto">
                            </div>

                            <div id="selected-layanan-display">
                                @if (old('layanan'))
                                    <div
                                        class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-200 text-[#1a2a6c] text-[13px] font-semibold px-2.5 py-1.5 rounded-lg mt-1.5">
                                        <i class="ti ti-check text-green-500 text-xs"></i>
                                        {{ old('layanan') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-[11.5px] text-slate-400 mt-1.5">Cari berdasarkan nama OPD, bidang, atau jenis
                            layanan</p>
                        @error('layanan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-slate-100">

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
                        <img id="foto-result" src="" alt="Foto Tamu">
                        <button type="button" id="btn-ulang-foto"
                            class="text-[11.5px] text-[#1B75BC] underline underline-offset-2 hover:opacity-70 transition">
                            Ambil ulang foto
                        </button>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="btn-submit"
                        class="w-full mt-2 py-3.5 bg-linear-to-r from-[#1a2a6c] to-[#1B75BC] text-white font-bold text-[15px] rounded-xl
                            flex items-center justify-center gap-2 hover:opacity-90 active:scale-[0.99] transition cursor-pointer tracking-wide">
                        <i class="ti ti-send text-[17px]"></i>
                        Kirim Data Kunjungan
                    </button>

                </form>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    <script>
        // DATA LAYANAN
        const LAYANAN_DATA = [
            // ── BADAN ──
            {
                id: 'bkpsdm',
                nama: 'Badan Kepegawaian Daerah dan Pengembangan Sumber Daya Manusia',
                desc: 'BKPSDM',
                icon: 'ti-users-group'
            },
            {
                id: 'kesbangpol',
                nama: 'Badan Kesatuan Bangsa dan Politik',
                desc: 'Kesbangpol',
                icon: 'ti-flag'
            },
            {
                id: 'bpbd',
                nama: 'Badan Penanggulangan Bencana Daerah',
                desc: 'BPBD',
                icon: 'ti-alert-triangle'
            },
            {
                id: 'bppkad',
                nama: 'Badan Pendapatan, Pengelolaan Keuangan dan Asset Daerah',
                desc: 'BPPKAD',
                icon: 'ti-receipt'
            },
            {
                id: 'bappeda',
                nama: 'Badan Perencanaan Pembangunan, Penelitian dan Pengembangan Daerah',
                desc: 'Bappeda',
                icon: 'ti-chart-dots'
            },
            // ── DINAS ──
            {
                id: 'ckpkp',
                nama: 'Dinas Cipta Karya, Perumahan dan Kawasan Permukiman',
                desc: 'CKPKP',
                icon: 'ti-building-community'
            },
            {
                id: 'dkbp3a',
                nama: 'Dinas Keluarga Berencana, Pemberdayaan Perempuan, dan Perlindungan Anak',
                desc: 'DKBP3A',
                icon: 'ti-heart'
            },
            {
                id: 'dispendukcapil',
                nama: 'Dinas Kependudukan dan Pencatatan Sipil',
                desc: 'Dispendukcapil',
                icon: 'ti-id-badge'
            },
            {
                id: 'dinkes',
                nama: 'Dinas Kesehatan',
                desc: 'Dinkes',
                icon: 'ti-heart-plus'
            },
            {
                id: 'diskominfo',
                nama: 'Dinas Komunikasi dan Informatika',
                desc: 'Diskominfo',
                icon: 'ti-device-laptop'
            },
            {
                id: 'diskopumdag',
                nama: 'Dinas Koperasi, Usaha Mikro, dan Perindag',
                desc: 'Diskopumdag',
                icon: 'ti-building-store'
            },
            {
                id: 'dlh',
                nama: 'Dinas Lingkungan Hidup',
                desc: 'DLH',
                icon: 'ti-leaf'
            },
            {
                id: 'disparekrafbudpora',
                nama: 'Dinas Pariwisata dan Ekonomi Kreatif, Kebudayaan, Kepemudaan, dan Olahraga',
                desc: 'Disparekrafbudpora',
                icon: 'ti-sailboat'
            },
            {
                id: 'dputr',
                nama: 'Dinas Pekerjaan Umum dan Tata Ruang',
                desc: 'DPUTR',
                icon: 'ti-crane'
            },
            {
                id: 'dpkp',
                nama: 'Dinas Pemadam Kebakaran dan Penyelamatan',
                desc: 'Damkar',
                icon: 'ti-flame'
            },
            {
                id: 'dpmd',
                nama: 'Dinas Pemberdayaan Masyarakat dan Desa',
                desc: 'DPMD',
                icon: 'ti-home'
            },
            {
                id: 'dpmptsp',
                nama: 'Dinas Penanaman Modal dan PTSP',
                desc: 'DPMPTSP',
                icon: 'ti-briefcase'
            },
            {
                id: 'dindik',
                nama: 'Dinas Pendidikan',
                desc: 'Dindik',
                icon: 'ti-school'
            },
            {
                id: 'dishub',
                nama: 'Dinas Perhubungan',
                desc: 'Dishub',
                icon: 'ti-car'
            },
            {
                id: 'dinkan',
                nama: 'Dinas Perikanan',
                desc: 'Dinkan',
                icon: 'ti-fish'
            },
            {
                id: 'dispusip',
                nama: 'Dinas Perpustakaan dan Kearsipan',
                desc: 'Dispusip',
                icon: 'ti-books'
            },
            {
                id: 'distan',
                nama: 'Dinas Pertanian',
                desc: 'Distan',
                icon: 'ti-plant'
            },
            {
                id: 'satpolpp',
                nama: 'Dinas Satpol PP',
                desc: 'Satpol PP',
                icon: 'ti-shield'
            },
            {
                id: 'dinsos',
                nama: 'Dinas Sosial',
                desc: 'Dinsos',
                icon: 'ti-hand-heart'
            },
            {
                id: 'disnaker',
                nama: 'Dinas Tenaga Kerja',
                desc: 'Disnaker',
                icon: 'ti-hammer'
            },
            {
                id: 'inspektorat',
                nama: 'Inspektorat',
                desc: 'Inspektorat',
                icon: 'ti-eye'
            },
            // ── KECAMATAN ──
            {
                id: 'kec-balongpanggang',
                nama: 'Kecamatan Balongpanggang',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-benjeng',
                nama: 'Kecamatan Benjeng',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-bungah',
                nama: 'Kecamatan Bungah',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-cerme',
                nama: 'Kecamatan Cerme',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-driyorejo',
                nama: 'Kecamatan Driyorejo',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-duduksampeyan',
                nama: 'Kecamatan Duduksampeyan',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-dukun',
                nama: 'Kecamatan Dukun',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-gresik',
                nama: 'Kecamatan Gresik',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-kebomas',
                nama: 'Kecamatan Kebomas',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-kedamean',
                nama: 'Kecamatan Kedamean',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-manyar',
                nama: 'Kecamatan Manyar',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-menganti',
                nama: 'Kecamatan Menganti',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-panceng',
                nama: 'Kecamatan Panceng',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-sangkapura',
                nama: 'Kecamatan Sangkapura',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-sidayu',
                nama: 'Kecamatan Sidayu',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-tambak',
                nama: 'Kecamatan Tambak',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-ujungpangkah',
                nama: 'Kecamatan Ujungpangkah',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            {
                id: 'kec-wringinanom',
                nama: 'Kecamatan Wringinanom',
                desc: 'Kecamatan',
                icon: 'ti-map-pin'
            },
            // ── SEKRETARIAT DAERAH ──
            {
                id: 'setda-admbang',
                nama: 'Sekretariat Daerah Bagian Administrasi Pembangunan',
                desc: 'Setda',
                icon: 'ti-building'
            },
            {
                id: 'setda-hukum',
                nama: 'Sekretariat Daerah Bagian Hukum',
                desc: 'Setda',
                icon: 'ti-scale'
            },
            {
                id: 'setda-kesra',
                nama: 'Sekretariat Daerah Bagian Kesejahteraan Rakyat',
                desc: 'Setda',
                icon: 'ti-users'
            },
            {
                id: 'setda-org',
                nama: 'Sekretariat Daerah Bagian Organisasi',
                desc: 'Setda',
                icon: 'ti-sitemap'
            },
            {
                id: 'setda-pbj',
                nama: 'Sekretariat Daerah Bagian Pengadaan Barang dan Jasa',
                desc: 'Setda',
                icon: 'ti-package'
            },
            {
                id: 'setda-ekon',
                nama: 'Sekretariat Daerah Bagian Perekonomian dan SDA',
                desc: 'Setda',
                icon: 'ti-chart-bar'
            },
            {
                id: 'setda-prokopim',
                nama: 'Sekretariat Daerah Bagian Protokol dan Komunikasi Pimpinan',
                desc: 'Setda',
                icon: 'ti-speakerphone'
            },
            {
                id: 'setda-tapem',
                nama: 'Sekretariat Daerah Bagian Tata Pemerintah',
                desc: 'Setda',
                icon: 'ti-landmark'
            },
            {
                id: 'setda-umum',
                nama: 'Sekretariat Daerah Bagian Umum',
                desc: 'Setda',
                icon: 'ti-clipboard'
            },
            {
                id: 'setwan',
                nama: 'Sekretariat Dewan',
                desc: 'Setwan',
                icon: 'ti-building-arch'
            },
            {
                id: 'sekda',
                nama: 'Sekretaris Daerah',
                desc: 'Sekda',
                icon: 'ti-user-circle'
            },
        ];

        // TOAST — didefinisikan PERTAMA agar bisa dipakai semua fungsi
        function showToast(type, title, msg, duration = 3000) {
            const container = document.getElementById('bt-toast-container');
            const isSuccess = type === 'success';
            const toast = document.createElement('div');

            toast.className = [
                'toast-enter relative flex items-start gap-2.5 bg-white rounded-xl pointer-events-auto',
                'px-4 py-3 min-w-[280px] max-w-xs overflow-hidden',
                'shadow-[0_4px_24px_rgba(0,0,0,0.15)]',
                isSuccess ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500',
            ].join(' ');

            toast.innerHTML = `
                <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-base
                    ${isSuccess ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                    <i class="ti ${isSuccess ? 'ti-check' : 'ti-alert-circle'}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13.5px] font-bold text-slate-800">${title}</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-snug">${msg}</p>
                </div>
                <div class="toast-progress absolute bottom-0 left-0 h-[3px] w-full ${isSuccess ? 'bg-green-500' : 'bg-red-500'}"
                    style="animation-duration: ${duration}ms"></div>`;

            container.appendChild(toast);
            requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('toast-show')));
            setTimeout(() => {
                toast.classList.remove('toast-show');
                toast.classList.add('toast-hide');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        }

        // KAMERA
        const overlay = document.getElementById('kamera-overlay');
        const videoEl = document.getElementById('video-preview');
        const canvasEl = document.getElementById('canvas');
        const fotoInput = document.getElementById('foto-input');
        const countdown = document.getElementById('countdown-ring');
        const previewWrap = document.getElementById('foto-preview-wrap');
        const fotoResult = document.getElementById('foto-result');
        const btnUlang = document.getElementById('btn-ulang-foto');
        const formTamu = document.getElementById('formTamu');

        let stream = null;
        let fotoSudahDiambil = false;

        async function inisialisasiKamera() {
            // Cek dukungan browser — tidak ada di HTTP selain localhost
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showToast('error', 'Kamera Tidak Didukung',
                    'Gunakan HTTPS atau localhost agar kamera dapat digunakan.', 6000);
                return;
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user'
                    }
                });
                videoEl.srcObject = stream;
            } catch (err) {
                let pesan = 'Foto tidak akan disertakan.';
                if (err.name === 'NotAllowedError') pesan = 'Izin kamera ditolak. Foto tidak akan disertakan.';
                if (err.name === 'NotFoundError') pesan = 'Kamera tidak ditemukan di perangkat ini.';
                if (err.name === 'NotReadableError') pesan = 'Kamera sedang dipakai aplikasi lain.';
                showToast('error', 'Kamera Tidak Aktif', pesan, 4000);
                console.warn('Kamera error:', err.name, err.message);
            }
        }

        function ambilFotoCountdown() {
            return new Promise((resolve) => {
                overlay.classList.add('aktif');
                let detik = 3;
                countdown.textContent = detik;

                const timer = setInterval(() => {
                    detik--;
                    if (detik > 0) {
                        countdown.textContent = detik;
                    } else {
                        clearInterval(timer);
                        countdown.textContent = '📸';

                        const ctx = canvasEl.getContext('2d');
                        ctx.save();
                        ctx.scale(-1, 1);
                        ctx.drawImage(videoEl, -canvasEl.width, 0, canvasEl.width, canvasEl.height);
                        ctx.restore();

                        const dataURL = canvasEl.toDataURL('image/jpeg', 0.85);
                        if (stream) stream.getTracks().forEach(t => t.stop());

                        setTimeout(() => {
                            overlay.classList.remove('aktif');
                            countdown.textContent = '';
                            resolve(dataURL);
                        }, 500);
                    }
                }, 1000);
            });
        }

        formTamu.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Ambil value field
            const nama = document.querySelector('[name="nama_tamu"]').value.trim();
            const instansi = document.querySelector('[name="asal_instansi"]').value.trim();
            const nohp = document.querySelector('[name="no_hp"]').value.trim();
            const layanan = document.querySelector('[name="layanan"]').value.trim();
            const keterangan = document.querySelector('[name="keterangan"]').value.trim();

            // Validasi sebelum kamera aktif
            if (!nama || !instansi || !nohp || !layanan || !keterangan) {

                showToast(
                    'error',
                    'Form Belum Lengkap',
                    'Mohon isi semua data terlebih dahulu.',
                    3500
                );

                return;
            }

            // Kalau foto sudah ada langsung submit
            if (fotoSudahDiambil) {
                this.submit();
                return;
            }

            // Kalau kamera tersedia → ambil foto
            if (stream) {
                try {
                    const dataURL = await ambilFotoCountdown();

                    fotoInput.value = dataURL;
                    fotoResult.src = dataURL;

                    previewWrap.style.display = 'flex';
                    fotoSudahDiambil = true;

                    formTamu.submit();

                } catch (err) {
                    formTamu.submit();
                }

            } else {
                this.submit();
            }
        });
        
        btnUlang.addEventListener('click', async function() {
            fotoSudahDiambil = false;
            fotoInput.value = '';
            previewWrap.style.display = 'none';

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showToast('error', 'Tidak Didukung', 'Kamera tidak dapat diakses di koneksi ini.', 4000);
                return;
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user'
                    }
                });
                videoEl.srcObject = stream;
                showToast('success', 'Kamera Aktif', 'Klik kirim ulang untuk ambil foto baru.', 2500);
            } catch {
                showToast('error', 'Kamera Gagal', 'Tidak dapat mengakses kamera.', 3000);
            }
        });

        // Inisialisasi kamera SETELAH DOM siap dan showToast sudah terdefinisi
        document.addEventListener('DOMContentLoaded', () => inisialisasiKamera());

        // SEARCH LAYANAN
        let selectedLayanan = null;

        const searchInput = document.getElementById('layanan-search');
        const dropdown = document.getElementById('layanan-dropdown');
        const clearBtn = document.getElementById('layanan-clear');
        const selectedDisp = document.getElementById('selected-layanan-display');
        const hiddenInput = document.getElementById('layanan-value');

        function highlight(text, query) {
            if (!query) return text;
            const idx = text.toLowerCase().indexOf(query.toLowerCase());
            if (idx === -1) return text;
            return text.slice(0, idx) +
                '<mark>' + text.slice(idx, idx + query.length) + '</mark>' +
                text.slice(idx + query.length);
        }

        function renderDropdown(query) {
            const filtered = LAYANAN_DATA.filter(l =>
                l.nama.toLowerCase().includes(query.toLowerCase()) ||
                l.desc.toLowerCase().includes(query.toLowerCase())
            );

            if (filtered.length === 0) {
                dropdown.innerHTML = `
                    <div class="py-5 text-center text-slate-400 text-sm">
                        <i class="ti ti-search-off block text-2xl mb-1.5 opacity-40"></i>
                        Layanan tidak ditemukan
                    </div>`;
            } else {
                dropdown.innerHTML = filtered.map(l => `
                    <div class="dd-item flex items-center gap-2.5 px-3.5 py-2.5 cursor-pointer hover:bg-blue-50 hover:text-[#1a2a6c] transition border-b border-slate-50 last:border-0"
                        data-id="${l.id}" data-nama="${l.nama}">
                        <div class="w-7 h-7 bg-blue-100 rounded-md flex items-center justify-center text-[13px] text-[#1a2a6c] shrink-0">
                            <i class="ti ${l.icon}"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-[13.5px]">${highlight(l.nama, query)}</div>
                            <div class="text-[11.5px] text-slate-400 mt-0.5">${l.desc}</div>
                        </div>
                    </div>
                `).join('');

                dropdown.querySelectorAll('.dd-item').forEach(item => {
                    item.addEventListener('mousedown', e => {
                        e.preventDefault();
                        const found = LAYANAN_DATA.find(l => l.id === item.dataset.id);
                        selectLayanan(found);
                    });
                });
            }
        }

        function openDropdown() {
            renderDropdown(searchInput.value);
            dropdown.classList.remove('hidden');
        }

        function closeDropdown() {
            dropdown.classList.add('hidden');
        }

        function selectLayanan(item) {
            selectedLayanan = item;
            hiddenInput.value = item.nama;
            searchInput.value = '';
            searchInput.placeholder = item.nama;
            searchInput.classList.remove('border-red-400', 'ring-red-100');
            searchInput.classList.add('border-slate-200');
            clearBtn.classList.remove('hidden');
            clearBtn.classList.add('flex');
            closeDropdown();
            selectedDisp.innerHTML = `
                <div class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-200 text-[#1a2a6c] text-[13px] font-semibold px-2.5 py-1.5 rounded-lg mt-1.5">
                    <i class="ti ${item.icon} text-xs"></i>
                    ${item.nama}
                    <i class="ti ti-check text-green-500 text-xs"></i>
                </div>`;
        }

        function clearLayanan() {
            selectedLayanan = null;
            hiddenInput.value = '';
            searchInput.value = '';
            searchInput.placeholder = 'Ketik nama OPD atau layanan...';
            clearBtn.classList.add('hidden');
            clearBtn.classList.remove('flex');
            selectedDisp.innerHTML = '';
            closeDropdown();
        }

        searchInput.addEventListener('focus', openDropdown);
        searchInput.addEventListener('input', () => {
            if (selectedLayanan) {
                selectedLayanan = null;
                hiddenInput.value = '';
                selectedDisp.innerHTML = '';
            }
            const hasVal = searchInput.value.length > 0;
            clearBtn.classList.toggle('hidden', !hasVal);
            clearBtn.classList.toggle('flex', hasVal);
            renderDropdown(searchInput.value);
            dropdown.classList.remove('hidden');
        });
        clearBtn.addEventListener('click', clearLayanan);
        document.addEventListener('click', e => {
            if (!document.getElementById('search-wrap').contains(e.target)) closeDropdown();
        });

        // CHAR COUNTER
        const keteranganEl = document.getElementById('f-keterangan');
        const charEl = document.getElementById('char-count');

        keteranganEl.addEventListener('input', () => {
            const len = keteranganEl.value.length;
            charEl.textContent = `${len} / 300`;
            charEl.className = 'text-[11px] ' + (len > 280 ? 'text-red-500' : len > 250 ? 'text-amber-500' :
                'text-slate-400');
        });

        // NO HP — hanya angka
        document.getElementById('f-nohp').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });

        // TOAST error validasi Laravel
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', 'Data Tidak Valid', 'Mohon periksa kembali isian form di bawah ini.', 4000);
            });
        @endif
    </script>

</body>

</html>
