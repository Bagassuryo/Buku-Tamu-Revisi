<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class GuestExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, WithStyles, ShouldAutoSize, WithColumnWidths
{
    protected Collection $guests;

    // Kita terima data dari Controller agar filter tetap jalan
    public function __construct(Collection $guests)
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
        return ['Instansi', 'Layanan', 'Nama', 'No HP', 'Instansi Asal', 'Keterangan', 'Tanggal', 'Datang', 'Pulang', 'Foto'];
    }

    // Memetakan kolom database ke Excel
    public function map($guest): array
    {
        return [
            $guest->instansi->nama ?? $guest->instansi_id ?? '-',
            $guest->layanan->nama_layanan ?? $guest->layanan ?? '-',
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
            if ($guest->foto && file_exists(public_path('storage/' . $guest->foto))) {
                $drawing = new Drawing();
                $drawing->setName('Foto');
                $drawing->setPath(public_path('storage/' . $guest->foto));
                $drawing->setHeight(70);
                $drawing->setCoordinates('J' . ($index + 2)); // Mulai dari baris 2
                $drawings[] = $drawing;
            }
        }
        return $drawings;
    }

    public function columnWidths(): array
    {
        return [
            'J' => 18, // Mengunci lebar kolom J (Foto) agar pas dengan gambar setinggi 70, sisanya (A-I) otomatis auto-size
        ];
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
