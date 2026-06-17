// ==========================================
// 1. TOAST COMPONENT
// ==========================================
let toastSedangTampil = false;

window.showToast = function (type, title, msg, duration = 3000) {
    if (toastSedangTampil) return;
    toastSedangTampil = true;

    const container = document.getElementById("bt-toast-container");
    const isSuccess = type === "success";
    const toast = document.createElement("div");

    toast.className = [
        "toast-enter relative flex items-start gap-2.5 bg-white rounded-xl pointer-events-auto",
        "px-4 py-3 min-w-[280px] max-w-xs overflow-hidden",
        "shadow-[0_4px_24px_rgba(0,0,0,0.15)]",
        isSuccess ? "border-l-4 border-green-500" : "border-l-4 border-red-500",
    ].join(" ");

    toast.innerHTML = `
        <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-base
            ${isSuccess ? "bg-green-100 text-green-600" : "bg-red-100 text-red-600"}">
            <i class="ti ${isSuccess ? "ti-check" : "ti-alert-circle"}"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[13.5px] font-bold text-slate-800">${title}</p>
            <p class="text-xs text-slate-500 mt-0.5 leading-snug">${msg}</p>
        </div>
        <div class="toast-progress absolute bottom-0 left-0 h-0.75 w-full ${isSuccess ? "bg-green-500" : "bg-red-500"}"
            style="animation-duration: ${duration}ms"></div>`;

    container.appendChild(toast);

    requestAnimationFrame(() =>
        requestAnimationFrame(() => toast.classList.add("toast-show")),
    );

    setTimeout(() => {
        toast.classList.remove("toast-show");
        toast.classList.add("toast-hide");
        setTimeout(() => {
            toast.remove();
            toastSedangTampil = false;
        }, 400);
    }, duration);
};

// ==========================================
// 2. KAMERA CONFIGURATION & METHODS
// ==========================================
const overlay = document.getElementById("kamera-overlay");
const videoEl = document.getElementById("video-preview");
const canvasEl = document.getElementById("canvas");
const fotoInput = document.getElementById("foto-input");
const countdown = document.getElementById("countdown-ring");
const previewWrap = document.getElementById("foto-preview-wrap");
const fotoResult = document.getElementById("foto-result");
const formTamu = document.getElementById("formTamu");

let stream = null;
let fotoSudahDiambil = false;

async function inisialisasiKamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        showToast(
            "error",
            "Kamera Tidak Didukung",
            "Gunakan HTTPS atau localhost agar kamera dapat digunakan.",
            6000,
        );
        return;
    }
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "user" },
        });
        videoEl.srcObject = stream;

        // SENIOR APPROACH: Ambil track video untuk memantau status secara real-time
        const videoTrack = stream.getVideoTracks()[0];

        // Jika izin dicabut user di tengah jalan tanpa refresh halaman
        videoTrack.onended = function () {
            stream = null;
            fotoSudahDiambil = false;
            showToast(
                "warning",
                "Akses Kamera Terputus",
                "Izin kamera dicabut. Mohon izinkan kembali akses kamera Anda.",
                5000,
            );
        };
    } catch (err) {
        stream = null;
        let pesan = "Foto tidak akan disertakan.";
        if (err.name === "NotAllowedError")
            pesan =
                "Izin kamera ditolak. Mohon berikan izin kamera untuk mengisi.";
        if (err.name === "NotFoundError")
            pesan = "Kamera tidak ditemukan di perangkat ini.";
        if (err.name === "NotReadableError")
            pesan = "Kamera sedang dipakai aplikasi lain.";
        showToast("error", "Kamera Tidak Aktif", pesan, 4000);
    }
}

function ambilFotoCountdown() {
    return new Promise((resolve) => {
        overlay.classList.add("aktif");
        let detik = 3;
        countdown.textContent = detik;

        const timer = setInterval(() => {
            detik--;
            if (detik > 0) {
                countdown.textContent = detik;
            } else {
                clearInterval(timer);
                countdown.textContent = "📸";

                const ctx = canvasEl.getContext("2d");
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(
                    videoEl,
                    -canvasEl.width,
                    0,
                    canvasEl.width,
                    canvasEl.height,
                );
                ctx.restore();

                const dataURL = canvasEl.toDataURL("image/jpeg", 0.85);

                // Matikan kamera setelah berhasil dijepret demi hemat batre/resource
                if (stream) {
                    stream.getTracks().forEach((t) => t.stop());
                }

                setTimeout(() => {
                    overlay.classList.remove("aktif");
                    countdown.textContent = "";
                    resolve(dataURL);
                }, 500);
            }
        }, 1000);
    });
}

// ==========================================
// 3. VALIDATION & FORM SUBMIT HANDLER
// ==========================================
formTamu.addEventListener("submit", async function (e) {
    e.preventDefault(); // Kunci submit bawaan form HTML

    // Ambil semua data input value
    const nama = document.querySelector('[name="nama_tamu"]').value.trim();
    const instansi = document
        .querySelector('[name="asal_instansi"]')
        .value.trim();
    const nohp = document.querySelector('[name="no_hp"]').value.trim();
    const instansi_id = document
        .querySelector('[name="instansi_id"]')
        .value.trim();
    const layanan = document.querySelector('[name="layanan_id"]').value.trim();
    const keterangan = document
        .querySelector('[name="keterangan"]')
        .value.trim();

    // ── Validasi Teks 1: Nama Tamu
    if (!nama) {
        showToast(
            "error",
            "Nama Belum Diisi",
            "Mohon isi nama lengkap Anda terlebih dahulu.",
            3500,
        );
        return;
    }
    if (nama.length < 3) {
        showToast(
            "error",
            "Nama Terlalu Pendek",
            "Nama minimal harus terdiri dari 3 karakter.",
            3500,
        );
        return;
    }
    if (!/^[a-zA-Z\s\.''\-]+$/.test(nama)) {
        showToast(
            "error",
            "Nama Tidak Valid",
            "Nama hanya boleh berisi huruf dan karakter umum nama.",
            3500,
        );
        return;
    }

    // ── Validasi Teks 2: Asal Instansi
    if (!instansi) {
        showToast(
            "error",
            "Asal Instansi Belum Diisi",
            "Mohon isi asal instansi/perusahaan Anda.",
            3500,
        );
        return;
    }
    if (instansi.length < 3) {
        showToast(
            "error",
            "Asal Instansi Terlalu Pendek",
            "Nama instansi minimal harus terdiri dari 3 karakter.",
            3500,
        );
        return;
    }
    if (!/^[a-zA-Z0-9\s\.\-]+$/.test(instansi)) {
        showToast(
            "error",
            "Asal Instansi Tidak Valid",
            "Nama instansi hanya boleh berisi huruf, angka, dan karakter umum.",
            3500,
        );
        return;
    }

    // ── Validasi Teks 3: Nomor HP
    if (!nohp) {
        showToast(
            "error",
            "No. HP Belum Diisi",
            "Mohon isi nomor HP aktif Anda.",
            3500,
        );
        return;
    }
    if (!validasiPanjangNoHp(nohp)) {
        showToast(
            "error",
            "No. HP Tidak Valid",
            "Nomor HP harus terdiri dari 10 hingga 15 digit angka.",
            4000,
        );
        return;
    }

    // ── Validasi Teks 4: Dropdown Instansi Tujuan
    if (!instansi_id) {
        showToast(
            "error",
            "Instansi Tujuan Belum Dipilih",
            "Mohon pilih instansi yang ingin Anda tuju.",
            3500,
        );
        return;
    }

    // ── Validasi Teks 5: Dropdown Layanan Kunjungan
    const subWrap = document.getElementById("layanan-wrap");
    if (!subWrap.classList.contains("hidden") && !layanan) {
        showToast(
            "error",
            "Jenis Layanan Belum Dipilih",
            "Mohon pilih jenis layanan kunjungan Anda.",
            3500,
        );
        return;
    }

    // ── Validasi Teks 6: Keterangan Keperluan
    if (!keterangan) {
        showToast(
            "error",
            "Keterangan Belum Diisi",
            "Mohon isi maksud atau keperluan kunjungan Anda.",
            3500,
        );
        return;
    }
    if (keterangan.length > 300) {
        showToast(
            "error",
            "Keterangan Terlalu Panjang",
            "Keterangan keperluan maksimal 300 karakter.",
            3500,
        );
        return;
    }
    if (!/^[a-zA-Z\s\.\-\,\'0-9]+$/.test(keterangan)) {
        showToast(
            "error",
            "Keterangan Tidak Valid",
            "Keterangan hanya boleh berisi huruf dan karakter umum.",
            3500,
        );
        return;
    }

    // ── BLOCKING CAMERA ACCORDING TO SENIOR METHOD ──────────────────

    // Skenario A: Jika foto sudah sukses diambil di aksi klik sebelumnya, langsung kirim ke Laravel
    if (fotoSudahDiambil) {
        this.submit();
        return;
    }

    // Skenario B: Jika stream mati/terputus/null (akibat efek videoTrack.onended tadi)
    if (!stream) {
        showToast(
            "error",
            "Kamera Tidak Aktif",
            "Foto wajib diambil sebelum mendaftar. Pastikan Anda memberikan akses izin kamera perangkat.",
            5000,
        );
        return; // Mengunci kiriman form!
    }

    // Skenario C: Kamera aman, mari kita mulai eksekusi countdown jepret
    try {
        const dataURL = await ambilFotoCountdown();

        // Proteksi tambahan anti data-corrupt
        if (!dataURL || dataURL === "data:,") {
            throw new Error("Hasil jepretan kanvas kosong.");
        }

        fotoInput.value = dataURL;
        fotoResult.src = dataURL;
        previewWrap.style.display = "flex";
        fotoSudahDiambil = true;

        // Semua aman, trigger submit data ke database backend!
        this.submit();
    } catch (err) {
        showToast(
            "error",
            "Gagal Menyimpan Foto",
            "Terjadi gangguan pada sistem webcam. Silakan muat ulang halaman dan coba kembali.",
            4000,
        );
    }
});

// ==========================================
// 4. HELPERS & EVENT LISTENERS
// ==========================================

// Character Counter Keperluan
const keteranganEl = document.getElementById("f-keterangan");
const charEl = document.getElementById("char-count");
keteranganEl.addEventListener("input", () => {
    const len = keteranganEl.value.length;
    charEl.textContent = `${len} / 300`;
    charEl.className =
        "text-[11px] " +
        (len > 280
            ? "text-red-500"
            : len > 250
              ? "text-amber-500"
              : "text-slate-400");
});

// Masking Input No HP
document.getElementById("f-nohp").addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9+\-\s]/g, "");
});

// Validasi Digit No HP
function validasiPanjangNoHp(nohp) {
    const digits = nohp.replace(/\D/g, "");
    return digits.length >= 10 && digits.length <= 15;
}

// Dom Ready Initializer
document.addEventListener("DOMContentLoaded", () => {
    inisialisasiKamera();
});
