<?php
// database/seeders/LayananSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $allLayanan = [
            'bkpsdm'           => ['Layanan Administrasi Kepegawaian', 'Pengembangan Kompetensi ASN', 'Mutasi dan Promosi Pegawai', 'Pensiun dan Pemberhentian', 'Penilaian Kinerja ASN', 'Konsultasi Kepegawaian', 'Lainnya'],
            'kesbangpol'       => ['Fasilitasi Organisasi Kemasyarakatan', 'Pendidikan Politik Masyarakat', 'Kewaspadaan Nasional', 'Pelayanan SKCK / Rekomendasi', 'Lainnya'],
            'bpbd'             => ['Pencegahan dan Kesiapsiagaan Bencana', 'Penanganan Darurat Bencana', 'Rehabilitasi dan Rekonstruksi', 'Pelaporan Kejadian Bencana', 'Lainnya'],
            'bppkad'           => ['Pendapatan Daerah / Pajak', 'Pengelolaan Keuangan Daerah', 'Pengelolaan Aset Daerah', 'Verifikasi dan Akuntansi', 'Konsultasi Anggaran', 'Lainnya'],
            'bappeda'          => ['Perencanaan Pembangunan Daerah', 'Musrenbang', 'Penelitian dan Pengembangan', 'Data dan Statistik Daerah', 'Konsultasi Program/Kegiatan', 'Lainnya'],
            'ckpkp'            => ['Perumahan dan Kawasan Permukiman', 'Bangunan Gedung dan IMB/PBG', 'Air Minum dan Sanitasi', 'Drainase Perkotaan', 'Penataan Ruang', 'Lainnya'],
            'dkbp3a'           => ['Layanan Keluarga Berencana (KB)', 'Perlindungan Perempuan dan Anak', 'Pemberdayaan Perempuan', 'Penanganan KDRT', 'Konseling Keluarga', 'Lainnya'],
            'dispendukcapil'   => ['Pembuatan / Cetak KTP-el', 'Pembuatan Kartu Keluarga (KK)', 'Akta Kelahiran', 'Akta Kematian', 'Akta Perkawinan / Perceraian', 'Surat Pindah / Datang', 'Surat Keterangan Kependudukan', 'Lainnya'],
            'dinkes'           => ['Pelayanan Kesehatan Dasar', 'Izin Sarana Kesehatan', 'Surveilans dan Imunisasi', 'Kesehatan Ibu dan Anak (KIA)', 'Promosi Kesehatan', 'Farmasi dan Alkes', 'Lainnya'],
            'diskominfo'       => ['Bidang SIP', 'Bidang SPBE', 'Bidang TI', 'Kepala Dinas Kominfo', 'Radio', 'Sekretariat', 'Sekretariat Dinas Kominfo'],
            'diskopumdag'      => ['Pemberdayaan Koperasi', 'Pengembangan Usaha Mikro', 'Perizinan Usaha Perdagangan', 'Pengawasan Pasar dan Metrologi', 'Pelatihan Wirausaha', 'Lainnya'],
            'dlh'              => ['Izin Lingkungan (AMDAL/UKL-UPL)', 'Pengawasan Lingkungan Hidup', 'Pengelolaan Sampah dan Limbah', 'Pencemaran dan Kerusakan Lingkungan', 'Penghijauan dan RTH', 'Lainnya'],
            'disparekrafbudpora' => ['Pariwisata dan Destinasi Wisata', 'Ekonomi Kreatif', 'Kebudayaan dan Kesenian', 'Kepemudaan', 'Olahraga', 'Lainnya'],
            'dputr'            => ['Jalan dan Jembatan', 'Sumber Daya Air / Irigasi', 'Tata Ruang dan Pemanfaatan Lahan', 'Bangunan Gedung Pemerintah', 'Lainnya'],
            'dpkp'             => ['Pemadaman Kebakaran', 'Penyelamatan / Rescue', 'Laporan Kebakaran', 'Sosialisasi Keselamatan Kebakaran', 'Lainnya'],
            'dpmd'             => ['Pemberdayaan Masyarakat Desa', 'Dana Desa / ADD', 'BUMDes', 'Pelatihan Aparatur Desa', 'Administrasi Desa', 'Lainnya'],
            'dpmptsp'          => ['Izin Usaha (NIB/OSS)', 'Izin Mendirikan Bangunan (IMB/PBG)', 'Izin Lingkungan', 'Konsultasi Perizinan', 'Pengaduan Layanan Perizinan', 'Lainnya'],
            'dindik'           => ['Pendidikan Anak Usia Dini (PAUD)', 'Pendidikan Dasar (SD/SMP)', 'Pendidikan Non Formal', 'Penerimaan Peserta Didik Baru (PPDB)', 'Data Pokok Pendidikan (Dapodik)', 'Izin Operasional Sekolah', 'Lainnya'],
            'dishub'           => ['Pengujian Kendaraan Bermotor (KIR)', 'Izin Trayek Angkutan', 'Manajemen Lalu Lintas', 'Perparkiran', 'Izin Pelabuhan / Terminal', 'Lainnya'],
            'dinkan'           => ['Budidaya Perikanan', 'Penangkapan Ikan', 'Pengolahan dan Pemasaran Hasil Perikanan', 'Izin Usaha Perikanan', 'Lainnya'],
            'dispusip'         => ['Layanan Peminjaman Buku', 'Keanggotaan Perpustakaan', 'Layanan Kearsipan Daerah', 'Deposit dan Alih Media', 'Lainnya'],
            'distan'           => ['Budidaya Tanaman Pangan', 'Hortikultura', 'Perkebunan', 'Peternakan', 'Penyuluhan Pertanian', 'Izin Usaha Pertanian', 'Lainnya'],
            'satpolpp'         => ['Penegakan Perda', 'Ketertiban Umum', 'Penertiban PKL', 'Laporan Pelanggaran', 'Lainnya'],
            'dinsos'           => ['Bantuan Sosial (Bansos)', 'Rehabilitasi Sosial', 'Perlindungan Sosial', 'Pemberdayaan Sosial', 'Verifikasi DTKS', 'Lainnya'],
            'disnaker'         => ['Penempatan Tenaga Kerja', 'Pelatihan dan Sertifikasi', 'Hubungan Industrial', 'Pengawasan Ketenagakerjaan', 'Izin Perusahaan / TKA', 'Lainnya'],
            'inspektorat'      => ['Pengaduan Masyarakat', 'Audit Internal', 'Reviu Laporan Keuangan', 'Pemeriksaan Khusus', 'Lainnya'],

            // Kecamatan (semua sama)
            'kec-balongpanggang' => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-benjeng'        => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-bungah'         => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-cerme'          => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-driyorejo'      => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-duduksampeyan'  => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-dukun'          => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-gresik'         => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-kebomas'        => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-kedamean'       => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-manyar'         => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-menganti'       => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-panceng'        => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-sangkapura'     => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-sidayu'         => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-tambak'         => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-ujungpangkah'   => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],
            'kec-wringinanom'    => ['Administrasi Kependudukan', 'Surat Keterangan Domisili', 'Rekomendasi Surat', 'Pelayanan Perizinan', 'Pengaduan Masyarakat', 'Lainnya'],

            // Setda
            'setda-admbang'  => ['Monitoring Pelaksanaan Pembangunan', 'Pelaporan Kegiatan', 'Konsultasi Administrasi Pembangunan', 'Lainnya'],
            'setda-hukum'    => ['Penyusunan Produk Hukum Daerah', 'Bantuan Hukum', 'Sosialisasi Peraturan', 'Konsultasi Hukum', 'Lainnya'],
            'setda-kesra'    => ['Layanan Keagamaan', 'Layanan Kesehatan Masyarakat', 'Layanan Pendidikan dan Sosial', 'Lainnya'],
            'setda-org'      => ['Evaluasi Kelembagaan', 'Ketatalaksanaan', 'Analisis Jabatan', 'Reformasi Birokrasi', 'Lainnya'],
            'setda-pbj'      => ['Konsultasi Pengadaan Barang/Jasa', 'LPSE / e-Procurement', 'Pemilihan Penyedia', 'Lainnya'],
            'setda-ekon'     => ['Koordinasi Perekonomian Daerah', 'Pengembangan SDA', 'Lainnya'],
            'setda-prokopim' => ['Protokol Pimpinan', 'Peliputan dan Dokumentasi', 'Komunikasi Publik', 'Lainnya'],
            'setda-tapem'    => ['Administrasi Pemerintahan', 'Kerjasama Daerah', 'Otonomi Daerah', 'Lainnya'],
            'setda-umum'     => ['Persuratan dan Kearsipan', 'Perlengkapan dan Rumah Tangga', 'Keprotokolan Umum', 'Lainnya'],
            'setwan'         => ['Layanan DPRD', 'Persidangan dan Risalah', 'Perundang-undangan', 'Humas DPRD', 'Lainnya'],
            'sekda'          => ['Audiensi / Pertemuan', 'Koordinasi Lintas OPD', 'Konsultasi Kebijakan', 'Lainnya'],
        ];

        $legacyLookup = [
            'bkpsdm' => ['desc' => 'BKPSDM'],
            'kesbangpol' => ['desc' => 'Kesbangpol'],
            'bpbd' => ['desc' => 'BPBD'],
            'bppkad' => ['desc' => 'BPPKAD'],
            'bappeda' => ['desc' => 'Bappeda'],
            'ckpkp' => ['desc' => 'CKPKP'],
            'dkbp3a' => ['desc' => 'DKBP3A'],
            'dispendukcapil' => ['desc' => 'Dispendukcapil'],
            'dinkes' => ['desc' => 'Dinkes'],
            'diskominfo' => ['desc' => 'Diskominfo'],
            'diskopumdag' => ['desc' => 'Diskopumdag'],
            'dlh' => ['desc' => 'DLH'],
            'disparekrafbudpora' => ['desc' => 'Disparekrafbudpora'],
            'dputr' => ['desc' => 'DPUTR'],
            'dpkp' => ['nama' => 'Dinas Pemadam Kebakaran dan Penyelamatan'],
            'dpmd' => ['desc' => 'DPMD'],
            'dpmptsp' => ['desc' => 'DPMPTSP'],
            'dindik' => ['desc' => 'Dindik'],
            'dishub' => ['desc' => 'Dishub'],
            'dinkan' => ['desc' => 'Dinkan'],
            'dispusip' => ['desc' => 'Dispusip'],
            'distan' => ['desc' => 'Distan'],
            'satpolpp' => ['desc' => 'Satpol PP'],
            'dinsos' => ['desc' => 'Dinsos'],
            'disnaker' => ['desc' => 'Disnaker'],
            'inspektorat' => ['desc' => 'Inspektorat'],
            'kec-balongpanggang' => ['nama' => 'Kecamatan Balongpanggang'],
            'kec-benjeng' => ['nama' => 'Kecamatan Benjeng'],
            'kec-bungah' => ['nama' => 'Kecamatan Bungah'],
            'kec-cerme' => ['nama' => 'Kecamatan Cerme'],
            'kec-driyorejo' => ['nama' => 'Kecamatan Driyorejo'],
            'kec-duduksampeyan' => ['nama' => 'Kecamatan Duduksampeyan'],
            'kec-dukun' => ['nama' => 'Kecamatan Dukun'],
            'kec-gresik' => ['nama' => 'Kecamatan Gresik'],
            'kec-kebomas' => ['nama' => 'Kecamatan Kebomas'],
            'kec-kedamean' => ['nama' => 'Kecamatan Kedamean'],
            'kec-manyar' => ['nama' => 'Kecamatan Manyar'],
            'kec-menganti' => ['nama' => 'Kecamatan Menganti'],
            'kec-panceng' => ['nama' => 'Kecamatan Panceng'],
            'kec-sangkapura' => ['nama' => 'Kecamatan Sangkapura'],
            'kec-sidayu' => ['nama' => 'Kecamatan Sidayu'],
            'kec-tambak' => ['nama' => 'Kecamatan Tambak'],
            'kec-ujungpangkah' => ['nama' => 'Kecamatan Ujungpangkah'],
            'kec-wringinanom' => ['nama' => 'Kecamatan Wringinanom'],
            'setda-admbang' => ['nama' => 'Sekretariat Daerah Bagian Administrasi Pembangunan'],
            'setda-hukum' => ['nama' => 'Sekretariat Daerah Bagian Hukum'],
            'setda-kesra' => ['nama' => 'Sekretariat Daerah Bagian Kesejahteraan Rakyat'],
            'setda-org' => ['nama' => 'Sekretariat Daerah Bagian Organisasi'],
            'setda-pbj' => ['nama' => 'Sekretariat Daerah Bagian Pengadaan Barang dan Jasa'],
            'setda-ekon' => ['nama' => 'Sekretariat Daerah Bagian Perekonomian dan SDA'],
            'setda-prokopim' => ['nama' => 'Sekretariat Daerah Bagian Protokol dan Komunikasi Pimpinan'],
            'setda-tapem' => ['nama' => 'Sekretariat Daerah Bagian Tata Pemerintah'],
            'setda-umum' => ['nama' => 'Sekretariat Daerah Bagian Umum'],
            'setwan' => ['nama' => 'Sekretariat Dewan'],
            'sekda' => ['nama' => 'Sekretaris Daerah'],
        ];

        foreach ($allLayanan as $kode => $layananList) {
            if (!isset($legacyLookup[$kode])) {
                continue;
            }

            $query = DB::table('instansi');
            $lookup = $legacyLookup[$kode];

            if (isset($lookup['nama'])) {
                $query->where('nama', $lookup['nama']);
            } elseif (isset($lookup['desc'])) {
                $query->where('desc', $lookup['desc']);
            }

            $instansi = $query->first();
            if (!$instansi) {
                continue;
            }

            foreach ($layananList as $urutan => $nama) {
                DB::table('layanan')->insert([
                    'instansi_id' => $instansi->id,
                    'nama_layanan' => $nama,
                    'urutan' => $urutan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
