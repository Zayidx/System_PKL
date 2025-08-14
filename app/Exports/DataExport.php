<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;
    protected $headers;

    public function __construct(array $data)
    {
        // Extract headers from the first row
        $this->headers = $data[0];
        // Remove headers from data
        array_shift($data);
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        // Dynamic column widths based on number of columns
        $widths = [];
        $columns = range('A', 'Z');
        
        for ($i = 0; $i < count($this->headers); $i++) {
            if (isset($columns[$i])) {
                // Set default widths based on column index
                switch ($i) {
                    case 0: // No
                        $widths[$columns[$i]] = 5;
                        break;
                    case 1: // Nama Siswa / Nama Perusahaan
                        $widths[$columns[$i]] = 25;
                        break;
                    case 2: // Kelas / Alamat
                        $widths[$columns[$i]] = 20;
                        break;
                    case 3: // Perusahaan / Email
                        $widths[$columns[$i]] = 25;
                        break;
                    case 4: // Pembimbing Sekolah / Kontak
                        $widths[$columns[$i]] = 20;
                        break;
                    case 5: // Pembimbing Perusahaan / Jumlah Siswa
                        $widths[$columns[$i]] = 15;
                        break;
                    case 6: // Tanggal Mulai / Pembimbing Sekolah
                        $widths[$columns[$i]] = 20;
                        break;
                    case 7: // Tanggal Selesai / Pembimbing Perusahaan
                        $widths[$columns[$i]] = 20;
                        break;
                    case 8: // Status Prakerin
                        $widths[$columns[$i]] = 15;
                        break;
                    default:
                        $widths[$columns[$i]] = 15;
                        break;
                }
            }
        }
        
        return $widths;
    }
}