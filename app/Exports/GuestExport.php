<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuestExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, WithStyles
{
    protected $guests;

    // Kita terima data dari Controller agar filter tetap jalan
    public function __construct($guests)
    {
        $this->guests = $guests;
    }

    public function collection()
    {
        return $this->guests;
    }

    // Menentukan Header
    public function headings(): array
    {
        return ['Layanan', 'Nama', 'No HP', 'Instansi', 'Keterangan', 'Tanggal', 'Datang', 'Pulang', 'Foto'];
    }

    // Memetakan kolom database ke Excel
    public function map($guest): array
    {
        return [
            $guest->layanan,
            $guest->nama_tamu,
            $guest->no_hp,
            $guest->asal_instansi,
            $guest->keterangan,
            $guest->tanggal,
            $guest->datang,
            $guest->pulang ?? '-',
            '', // Kosongkan kolom foto untuk nanti diisi gambar
        ];
    }

    // Menangani Gambar
    public function drawings()
    {
        $drawings = [];
        foreach ($this->guests as $index => $guest) {
            // Pastikan kolom foto ada isinya dan file fisik ada
            if ($guest->foto && file_exists(public_path('storage/foto/' . $guest->foto))) {
                $drawing = new Drawing();
                $drawing->setName('Foto');
                $drawing->setPath(public_path('storage/foto/' . $guest->foto));
                $drawing->setHeight(70);
                $drawing->setCoordinates('I' . ($index + 2)); // Mulai dari baris 2
                $drawings[] = $drawing;
            }
        }
        return $drawings;
    }

    // Mengatur tinggi baris agar gambar muat
    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->guests) + 1;
        for ($i = 2; $i <= $rowCount; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(60);
        }
    }
}