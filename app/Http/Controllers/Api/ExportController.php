<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MahasiswaService;
use App\Exceptions\MahasiswaException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function __construct(
        protected MahasiswaService $mahasiswaService
    ) {}

    public function exportCsv()
    {
        $data = $this->mahasiswaService->getAllForAlgorithm();

        if (empty($data)) {
            throw MahasiswaException::dataKosong();
        }

        $headers = array_keys((new \App\Models\Mahasiswa())->toExportArray());
        $csv     = implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $mahasiswa = \App\Models\Mahasiswa::find($row['id']);
            if ($mahasiswa) {
                $csv .= implode(',', array_map(function ($val) {
                    return '"' . str_replace('"', '""', $val ?? '') . '"';
                }, $mahasiswa->toExportArray())) . "\n";
            }
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mahasiswa-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function exportTxt()
    {
        $data = $this->mahasiswaService->getAllForAlgorithm();

        if (empty($data)) {
            throw MahasiswaException::dataKosong();
        }

        $txt = "DATA MAHASISWA\n";
        $txt .= str_repeat('=', 50) . "\n";
        $txt .= "Diexport pada: " . now()->format('d/m/Y H:i:s') . "\n\n";

        foreach ($data as $i => $row) {
            $mahasiswa = \App\Models\Mahasiswa::find($row['id']);
            if ($mahasiswa) {
                $export = $mahasiswa->toExportArray();
                $txt .= "Mahasiswa #" . ($i + 1) . "\n";
                $txt .= str_repeat('-', 30) . "\n";
                foreach ($export as $key => $value) {
                    $txt .= str_pad($key, 20, ' ') . ": {$value}\n";
                }
                $txt .= "\n";
            }
        }

        return response($txt, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => 'attachment; filename="mahasiswa-' . now()->format('Y-m-d') . '.txt"',
        ]);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path    = $request->file('file')->store('imports');
        $content = Storage::get($path);
        $lines   = array_filter(explode("\n", $content));
        $imported = 0;

        // Skip header row, parse CSV
        $headers = str_getcsv(array_shift($lines));
        $fieldMap = [
            'NIM' => 'nim', 'Nama' => 'nama', 'Jurusan' => 'jurusan',
            'Fakultas' => 'fakultas', 'Email' => 'email', 'Nomor HP' => 'nomor_hp',
            'Alamat' => 'alamat', 'Tanggal Lahir' => 'tanggal_lahir', 'Jenis Kelamin' => 'jenis_kelamin',
        ];

        foreach ($lines as $line) {
            $values = str_getcsv($line);
            if (count($values) !== count($headers)) {
                continue;
            }
            $data = [];
            foreach ($headers as $i => $header) {
                $field = $fieldMap[trim($header)] ?? null;
                if ($field) {
                    $data[$field] = $values[$i] ?? null;
                }
            }
            if (!empty($data['nim'])) {
                $data['user_id'] = $request->user()->id;
                $this->mahasiswaService->create($data);
                $imported++;
            }
        }

        Storage::delete($path);

        return response()->json([
            'success'  => true,
            'message'  => "Berhasil mengimpor {$imported} data mahasiswa.",
            'imported' => $imported,
        ]);
    }
}
