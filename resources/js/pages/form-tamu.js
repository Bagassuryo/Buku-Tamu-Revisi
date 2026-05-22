   import {OPD_DATA, LAYANAN_DATA} from './data-form-tamu.js'
   
   // TOAST
        window.showToast = function (type, title, msg, duration = 3000) {

            if (toastSedangTampil) return;

            toastSedangTampil = true;

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
        <div class="toast-progress absolute bottom-0 left-0 h-0.75 w-full ${isSuccess ? 'bg-green-500' : 'bg-red-500'}"
            style="animation-duration: ${duration}ms"></div>`;
            container.appendChild(toast);
            requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('toast-show')));
            setTimeout(() => {
                toast.classList.remove('toast-show');
                toast.classList.add('toast-hide');
                setTimeout(() => {
                    toast.remove();
                    toastSedangTampil = false;
                }, 400);
            }, duration);
        }

            // ═══════════════════════════════════════════════════
            // KAMERA
            // ═══════════════════════════════════════════════════
            const overlay = document.getElementById('kamera-overlay');
            const videoEl = document.getElementById('video-preview');
            const canvasEl = document.getElementById('canvas');
            const fotoInput = document.getElementById('foto-input');
            const countdown = document.getElementById('countdown-ring');
            const previewWrap = document.getElementById('foto-preview-wrap');
            const fotoResult = document.getElementById('foto-result');
            const btnUlang = document.getElementById('btn-ulang-foto');
            const formTamu = document.getElementById('formTamu');

            let toastSedangTampil = false;
            let stream = null;
            let fotoSudahDiambil = false;

            async function inisialisasiKamera() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    showToast('error', 'Kamera Tidak Didukung',
                        'Gunakan HTTPS atau localhost agar kamera dapat digunakan.',
                        6000);
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
                const nama = document.querySelector('[name="nama_tamu"]').value.trim();
                const instansi = document.querySelector('[name="asal_instansi"]').value.trim();
                const nohp = document.querySelector('[name="no_hp"]').value.trim();
                const opd = document.querySelector('[name="opd"]').value.trim();
                const layanan = document.querySelector('[name="layanan"]').value.trim();
                const keterangan = document.querySelector('[name="keterangan"]').value.trim();

                if (!nama || !instansi || !nohp || !opd || !layanan || !keterangan) {
                    showToast('error', 'Form Belum Lengkap', 'Mohon isi semua data terlebih dahulu.', 3500);
                    return;
                }

                // Validasi layanan hanya jika wrap-nya tampil
                const subWrap = document.getElementById('layanan-wrap');
                if (!subWrap.classList.contains('hidden') && !layanan) {
                    showToast('error', 'Jenis Layanan Belum Dipilih', 'Mohon pilih jenis layanan yang dituju.',
                        3500);
                    return;
                }

                if (fotoSudahDiambil) {
                    this.submit();
                    return;
                }

                if (stream) {
                    try {
                        const dataURL = await ambilFotoCountdown();
                        fotoInput.value = dataURL;
                        fotoResult.src = dataURL;
                        previewWrap.style.display = 'flex';
                        fotoSudahDiambil = true;
                        formTamu.submit();
                    } catch {
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

            document.addEventListener('DOMContentLoaded', () => inisialisasiKamera());

            // ═══════════════════════════════════════════════════
            // SEARCH LAYANAN + SUB LAYANAN
            // ═══════════════════════════════════════════════════
            let selectedOPD = null;

            const searchInput = document.getElementById('opd-search');
            const dropdown = document.getElementById('opd-dropdown');
            const clearBtn = document.getElementById('opd-clear');
            const selectedDisp = document.getElementById('selected-opd-display');
            const hiddenInput = document.getElementById('opd-value');

            // Layanan elements
            const subWrap = document.getElementById('layanan-wrap');
            const subSelect = document.getElementById('layanan-select');

            function highlight(text, query) {
                if (!query) return text;
                const idx = text.toLowerCase().indexOf(query.toLowerCase());
                if (idx === -1) return text;
                return text.slice(0, idx) +
                    '<mark>' + text.slice(idx, idx + query.length) + '</mark>' +
                    text.slice(idx + query.length);
            }

            function renderDropdown(query) {
                const filtered = OPD_DATA.filter(l =>
                    l.nama.toLowerCase().includes(query.toLowerCase()) ||
                    l.desc.toLowerCase().includes(query.toLowerCase())
                );
                if (filtered.length === 0) {
                    dropdown.innerHTML = `
            <div class="py-5 text-center text-slate-400 text-sm">
                <i class="ti ti-search-off block text-2xl mb-1.5 opacity-40"></i>
                OPD tidak ditemukan
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
                            const found = OPD_DATA.find(l => l.id === item.dataset.id);
                            selectOPD(found);
                        });
                    });
                }
            }

            function loadLayanan(opdId) {
                const list = LAYANAN_DATA[opdId] || [];
                subSelect.innerHTML = '<option value="">-- Pilih Jenis Layanan --</option>';
                if (list.length > 0) {
                    list.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item;
                        opt.textContent = item;
                        subSelect.appendChild(opt);
                    });
                    // Animasi muncul
                    subWrap.classList.remove('hidden');
                    subWrap.style.opacity = '0';
                    subWrap.style.transform = 'translateY(-6px)';
                    subWrap.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    requestAnimationFrame(() => requestAnimationFrame(() => {
                        subWrap.style.opacity = '1';
                        subWrap.style.transform = 'translateY(0)';
                    }));
                } else {
                    subWrap.classList.add('hidden');
                }
            }

            function openDropdown() {
                renderDropdown(searchInput.value);
                dropdown.classList.remove('hidden');
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
            }

            function selectOPD(item) {
                selectedOPD = item;
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

                // Muat sub layanan
                loadLayanan(item.id);
            }

            function clearOPD() {
                selectedOPD = null;
                hiddenInput.value = '';
                searchInput.value = '';
                searchInput.placeholder = 'Ketik nama OPD atau layanan...';
                clearBtn.classList.add('hidden');
                clearBtn.classList.remove('flex');
                selectedDisp.innerHTML = '';
                closeDropdown();
                // Sembunyikan sub layanan
                subWrap.classList.add('hidden');
                subSelect.innerHTML = '<option value="">-- Pilih Jenis Layanan --</option>';
            }

            searchInput.addEventListener('focus', openDropdown);
            searchInput.addEventListener('input', () => {
                if (selectedOPD) {
                    selectedOPD = null;
                    hiddenInput.value = '';
                    selectedDisp.innerHTML = '';
                    subWrap.classList.add('hidden');
                }
                const hasVal = searchInput.value.length > 0;
                clearBtn.classList.toggle('hidden', !hasVal);
                clearBtn.classList.toggle('flex', hasVal);
                renderDropdown(searchInput.value);
                dropdown.classList.remove('hidden');
            });
            clearBtn.addEventListener('click', clearOPD);
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

            // NO HP
            document.getElementById('f-nohp').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9+\-\s]/g, '');
            });

