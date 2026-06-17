<?php
// database/seeders/InstansiSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ── BADAN ──
            ['kode' => 'bkpsdm',     'nama' => 'Badan Kepegawaian Daerah dan Pengembangan Sumber Daya Manusia', 'desc' => 'BKPSDM',],
            ['kode' => 'kesbangpol', 'nama' => 'Badan Kesatuan Bangsa dan Politik',                             'desc' => 'Kesbangpol',],
            ['kode' => 'bpbd',       'nama' => 'Badan Penanggulangan Bencana Daerah',                           'desc' => 'BPBD',],
            ['kode' => 'bppkad',     'nama' => 'Badan Pendapatan, Pengelolaan Keuangan dan Asset Daerah',       'desc' => 'BPPKAD',],
            ['kode' => 'bappeda',    'nama' => 'Badan Perencanaan Pembangunan, Penelitian dan Pengembangan Daerah', 'desc' => 'Bappeda',],

            // ── DINAS ──
            ['kode' => 'ckpkp',            'nama' => 'Dinas Cipta Karya, Perumahan dan Kawasan Permukiman',                              'desc' => 'CKPKP',],
            ['kode' => 'dkbp3a',           'nama' => 'Dinas Keluarga Berencana, Pemberdayaan Perempuan, dan Perlindungan Anak',          'desc' => 'DKBP3A',],
            ['kode' => 'dispendukcapil',   'nama' => 'Dinas Kependudukan dan Pencatatan Sipil',                                         'desc' => 'Dispendukcapil',],
            ['kode' => 'dinkes',           'nama' => 'Dinas Kesehatan',                                                                  'desc' => 'Dinkes',],
            ['kode' => 'diskominfo',       'nama' => 'Dinas Komunikasi dan Informatika',                                                 'desc' => 'Diskominfo',],
            ['kode' => 'diskopumdag',      'nama' => 'Dinas Koperasi, Usaha Mikro, dan Perindag',                                        'desc' => 'Diskopumdag',],
            ['kode' => 'dlh',              'nama' => 'Dinas Lingkungan Hidup',                                                           'desc' => 'DLH',],
            ['kode' => 'disparekrafbudpora', 'nama' => 'Dinas Pariwisata dan Ekonomi Kreatif, Kebudayaan, Kepemudaan, dan Olahraga',       'desc' => 'Disparekrafbudpora',],
            ['kode' => 'dputr',            'nama' => 'Dinas Pekerjaan Umum dan Tata Ruang',                                              'desc' => 'DPUTR',],
            ['kode' => 'dpkp',             'nama' => 'Dinas Pemadam Kebakaran dan Penyelamatan',                                         'desc' => 'Damkar',],
            ['kode' => 'dpmd',             'nama' => 'Dinas Pemberdayaan Masyarakat dan Desa',                                           'desc' => 'DPMD',],
            ['kode' => 'dpmptsp',          'nama' => 'Dinas Penanaman Modal dan PTSP',                                                   'desc' => 'DPMPTSP',],
            ['kode' => 'dishub',           'nama' => 'Dinas Perhubungan',                                                                'desc' => 'Dishub',],
            ['kode' => 'dinkan',           'nama' => 'Dinas Perikanan',                                                                  'desc' => 'Dinkan',],
            ['kode' => 'dispusip',         'nama' => 'Dinas Perpustakaan dan Kearsipan',                                                 'desc' => 'Dispusip',],
            ['kode' => 'distan',           'nama' => 'Dinas Pertanian',                                                                  'desc' => 'Distan',],
            ['kode' => 'satpolpp',         'nama' => 'Dinas Satpol PP',                                                                  'desc' => 'Satpol PP',],
            ['kode' => 'dinsos',           'nama' => 'Dinas Sosial',                                                                     'desc' => 'Dinsos',],
            ['kode' => 'disnaker',         'nama' => 'Dinas Tenaga Kerja',                                                               'desc' => 'Disnaker',],
            ['kode' => 'inspektorat',      'nama' => 'Inspektorat',                                                                      'desc' => 'Inspektorat',],

            // ── KECAMATAN ──
            ['kode' => 'kec-balongpanggang', 'nama' => 'Kecamatan Balongpanggang', 'desc' => 'Kecamatan'],
            ['kode' => 'kec-benjeng',        'nama' => 'Kecamatan Benjeng',        'desc' => 'Kecamatan'],
            ['kode' => 'kec-bungah',         'nama' => 'Kecamatan Bungah',         'desc' => 'Kecamatan'],
            ['kode' => 'kec-cerme',          'nama' => 'Kecamatan Cerme',          'desc' => 'Kecamatan'],
            ['kode' => 'kec-driyorejo',      'nama' => 'Kecamatan Driyorejo',      'desc' => 'Kecamatan'],
            ['kode' => 'kec-duduksampeyan',  'nama' => 'Kecamatan Duduksampeyan', 'desc' => 'Kecamatan'],
            ['kode' => 'kec-dukun',          'nama' => 'Kecamatan Dukun',          'desc' => 'Kecamatan'],
            ['kode' => 'kec-gresik',         'nama' => 'Kecamatan Gresik',         'desc' => 'Kecamatan'],
            ['kode' => 'kec-kebomas',        'nama' => 'Kecamatan Kebomas',        'desc' => 'Kecamatan'],
            ['kode' => 'kec-kedamean',       'nama' => 'Kecamatan Kedamean',       'desc' => 'Kecamatan'],
            ['kode' => 'kec-manyar',         'nama' => 'Kecamatan Manyar',         'desc' => 'Kecamatan'],
            ['kode' => 'kec-menganti',       'nama' => 'Kecamatan Menganti',       'desc' => 'Kecamatan'],
            ['kode' => 'kec-panceng',        'nama' => 'Kecamatan Panceng',        'desc' => 'Kecamatan'],
            ['kode' => 'kec-sangkapura',     'nama' => 'Kecamatan Sangkapura',     'desc' => 'Kecamatan'],
            ['kode' => 'kec-sidayu',         'nama' => 'Kecamatan Sidayu',         'desc' => 'Kecamatan'],
            ['kode' => 'kec-tambak',         'nama' => 'Kecamatan Tambak',         'desc' => 'Kecamatan'],
            ['kode' => 'kec-ujungpangkah',   'nama' => 'Kecamatan Ujungpangkah',  'desc' => 'Kecamatan'],
            ['kode' => 'kec-wringinanom',    'nama' => 'Kecamatan Wringinanom',   'desc' => 'Kecamatan'],

            // ── SETDA ──
            ['kode' => 'setda-admbang',   'nama' => 'Sekretariat Daerah Bagian Administrasi Pembangunan',   'desc' => 'Setda'],
            ['kode' => 'setda-hukum',     'nama' => 'Sekretariat Daerah Bagian Hukum',                      'desc' => 'Setda'],
            ['kode' => 'setda-kesra',     'nama' => 'Sekretariat Daerah Bagian Kesejahteraan Rakyat',        'desc' => 'Setda'],
            ['kode' => 'setda-org',       'nama' => 'Sekretariat Daerah Bagian Organisasi',                  'desc' => 'Setda'],
            ['kode' => 'setda-pbj',       'nama' => 'Sekretariat Daerah Bagian Pengadaan Barang dan Jasa',   'desc' => 'Setda'],
            ['kode' => 'setda-ekon',      'nama' => 'Sekretariat Daerah Bagian Perekonomian dan SDA',        'desc' => 'Setda'],
            ['kode' => 'setda-prokopim',  'nama' => 'Sekretariat Daerah Bagian Protokol dan Komunikasi Pimpinan', 'desc' => 'Setda'],
            ['kode' => 'setda-tapem',     'nama' => 'Sekretariat Daerah Bagian Tata Pemerintah',             'desc' => 'Setda'],
            ['kode' => 'setda-umum',      'nama' => 'Sekretariat Daerah Bagian Umum',                        'desc' => 'Setda'],
            ['kode' => 'setwan',          'nama' => 'Sekretariat Dewan',                                      'desc' => 'Setwan'],
            ['kode' => 'sekda',           'nama' => 'Sekretaris Daerah',                                      'desc' => 'Sekda'],
        ];

        foreach ($data as $item) {
            DB::table('instansi')->insert([
                'nama'       => $item['nama'],
                'desc'       => $item['desc'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
