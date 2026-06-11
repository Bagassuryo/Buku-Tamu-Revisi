// TOAST
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

// ═══════════════════════════════════════════════════
// KAMERA
// ═══════════════════════════════════════════════════
const overlay = document.getElementById("kamera-overlay");
const videoEl = document.getElementById("video-preview");
const canvasEl = document.getElementById("canvas");
const fotoInput = document.getElementById("foto-input");
const countdown = document.getElementById("countdown-ring");
const previewWrap = document.getElementById("foto-preview-wrap");
const fotoResult = document.getElementById("foto-result");
const formTamu = document.getElementById("formTamu");

let toastSedangTampil = false;
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
            video: {
                facingMode: "user",
            },
        });
        videoEl.srcObject = stream;
    } catch (err) {
        let pesan = "Foto tidak akan disertakan.";
        if (err.name === "NotAllowedError")
            pesan = "Izin kamera ditolak. Foto tidak akan disertakan.";
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
                if (stream) stream.getTracks().forEach((t) => t.stop());
                setTimeout(() => {
                    overlay.classList.remove("aktif");
                    countdown.textContent = "";
                    resolve(dataURL);
                }, 500);
            }
        }, 1000);
    });
}

formTamu.addEventListener("submit", async function (e) {
    e.preventDefault();

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

    // ── 1. VALIDASI NAMA TAMU ─────────────────────────────────────────
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

    // ── 2. VALIDASI ASAL INSTANSI TAMU ────────────────────────────────
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
            "Nama instansi hanya boleh berisi huruf, angka, dan karakter umum nama.",
            3500,
        );
        return;
    }
    // ── 3. VALIDASI NOMOR HP ──────────────────────────────────────────
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

    // ── 4. VALIDASI INSTANSI TUJUAN (DROPDOWN) ────────────────────────
    if (!instansi_id) {
        showToast(
            "error",
            "Instansi Tujuan Belum Dipilih",
            "Mohon pilih instansi yang ingin Anda tuju.",
            3500,
        );
        return;
    }

    // ── 5. VALIDASI LAYANAN TUJUAN (JIKA DITAMPILKAN) ──────────────────
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

    // ── 6. VALIDASI KETERANGAN KEPERLUAN ─────────────────────────────
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

    // ── SEMUA VALIDASI AMAN, PROSES JEPRET KAMERA ────────────────────
    if (fotoSudahDiambil) {
        this.submit();
        return;
    }

    if (stream) {
        try {
            const dataURL = await ambilFotoCountdown();
            fotoInput.value = dataURL;
            fotoResult.src = dataURL;
            previewWrap.style.display = "flex";
            fotoSudahDiambil = true;
            formTamu.submit();
        } catch {
            formTamu.submit();
        }
    } else {
        this.submit();
    }
});

// CHAR COUNTER
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

// NO HP - Input masker agar hanya menerima angka, +, -, dan spasi secara langsung
document.getElementById("f-nohp").addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9+\-\s]/g, "");
});

// Fungsi khusus mengecek panjang digit nomor HP (Sisa dari dead code yang dibersihkan)
function validasiPanjangNoHp(nohp) {
    const digits = nohp.replace(/\D/g, "");
    return digits.length >= 10 && digits.length <= 15;
}

// ═══════════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", () => {
    inisialisasiKamera();
});
