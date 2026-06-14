<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Exceptions\MahasiswaException;
use App\Algorithms\SearchInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * MahasiswaService - OOP: Encapsulation + Service Pattern
 * Berisi seluruh business logic terkait data mahasiswa
 *
 * CRUD Time Complexity:
 * - Create : O(1) - single INSERT
 * - Read   : O(log n) - dengan index pada primary key
 * - Update : O(log n) - find by PK + UPDATE
 * - Delete : O(log n) - find by PK + DELETE
 * - List   : O(n) - full scan + O(k log k) untuk sort
 */
class MahasiswaService
{
    /**
     * Ambil daftar mahasiswa dengan filter, search, sort, dan pagination
     * Time Complexity: O(n) scan + O(k log k) sort di DB level
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        try {
            $query = Mahasiswa::query()->with('user:id,name');

            if (!empty($filters['search'])) {
                $query->search($filters['search']);
            }

            if (!empty($filters['jurusan'])) {
                $query->byJurusan($filters['jurusan']);
            }

            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortDir = $filters['sort_dir'] ?? 'desc';
            $allowedSorts = ['nama', 'nim', 'jurusan', 'created_at', 'tanggal_lahir'];

            if (in_array($sortBy, $allowedSorts, true)) {
                $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
            }

            $perPage = min((int)($filters['per_page'] ?? 10), 100);

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('MahasiswaService::getAll error', [
                'filters' => $filters,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Buat mahasiswa baru
     * Time Complexity: O(1) - single INSERT + O(n) untuk upload foto
     *
     * @throws MahasiswaException
     */
    public function create(array $data, ?UploadedFile $foto = null): Mahasiswa
    {
        $this->normalizeData($data);

        if (Mahasiswa::where('nim', $data['nim'])->exists()) {
            throw MahasiswaException::nimSudahAda($data['nim']);
        }

        if (!Mahasiswa::validateNim($data['nim'])) {
            throw MahasiswaException::formatTidakValid('NIM', 'Harus berupa 7-15 digit angka');
        }

        if (!empty($data['nomor_hp']) && !Mahasiswa::validateNomorHp($data['nomor_hp'])) {
            throw MahasiswaException::formatTidakValid('Nomor HP', 'Format tidak valid (+62 atau 08 + 8-12 digit)');
        }

        return DB::transaction(function () use ($data, $foto) {
            try {
                if ($foto) {
                    $data['foto'] = $this->uploadFoto($foto);
                }

                $mahasiswa = Mahasiswa::create($data);
                $this->logActivity('CREATE', $mahasiswa);

                return $mahasiswa;
            } catch (MahasiswaException $e) {
                throw $e;
            } catch (\Exception $e) {
                Log::error('MahasiswaService::create error', [
                    'data' => $data,
                    'error' => $e->getMessage(),
                ]);
                throw new MahasiswaException('Gagal membuat data mahasiswa: ' . $e->getMessage());
            }
        });
    }

    /**
     * Update data mahasiswa
     * Time Complexity: O(log n) - find by PK + UPDATE
     *
     * @throws MahasiswaException
     */
    public function update(int $id, array $data, ?UploadedFile $foto = null): Mahasiswa
    {
        $mahasiswa = Mahasiswa::find($id);
        if (!$mahasiswa) {
            throw MahasiswaException::mahasiswaTidakDitemukan($id);
        }

        $this->normalizeData($data);

        if (!empty($data['nim'])) {
            $nimExists = Mahasiswa::where('nim', $data['nim'])
                ->where('id', '!=', $id)
                ->exists();

            if ($nimExists) {
                throw MahasiswaException::nimSudahAda($data['nim']);
            }
        }

        return DB::transaction(function () use ($mahasiswa, $data, $foto) {
            try {
                if ($foto) {
                    if ($mahasiswa->foto) {
                        Storage::disk('public')->delete($mahasiswa->foto);
                    }
                    $data['foto'] = $this->uploadFoto($foto);
                }

                $mahasiswa->update($data);
                $this->logActivity('UPDATE', $mahasiswa);

                return $mahasiswa->fresh();
            } catch (MahasiswaException $e) {
                throw $e;
            } catch (\Exception $e) {
                Log::error('MahasiswaService::update error', [
                    'id' => $mahasiswa->id,
                    'error' => $e->getMessage(),
                ]);
                throw new MahasiswaException('Gagal mengupdate data mahasiswa.');
            }
        });
    }

    /**
     * Hapus mahasiswa (soft delete)
     * Time Complexity: O(log n) - find by PK + soft DELETE
     *
     * @throws MahasiswaException
     */
    public function delete(int $id): bool
    {
        $mahasiswa = Mahasiswa::find($id);
        if (!$mahasiswa) {
            throw MahasiswaException::mahasiswaTidakDitemukan($id);
        }

        try {
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }

            $this->logActivity('DELETE', $mahasiswa);

            return $mahasiswa->delete();
        } catch (\Exception $e) {
            Log::error('MahasiswaService::delete error', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw new MahasiswaException('Gagal menghapus data mahasiswa.');
        }
    }

    /**
     * Ambil detail satu mahasiswa
     * Time Complexity: O(log n) - index scan by PK
     *
     * @throws MahasiswaException
     */
    public function findById(int $id): Mahasiswa
    {
        $mahasiswa = Mahasiswa::with('user:id,name')->find($id);

        if (!$mahasiswa) {
            throw MahasiswaException::mahasiswaTidakDitemukan($id);
        }

        return $mahasiswa;
    }

    /**
     * Upload foto mahasiswa ke storage
     * Time Complexity: O(n) - n = ukuran file
     *
     * @throws MahasiswaException
     */
    private function uploadFoto(UploadedFile $foto): string
    {
        try {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

            if (!in_array($foto->getMimeType(), $allowedMimes, true)) {
                throw MahasiswaException::uploadFotoGagal('Format tidak didukung. Gunakan JPG, PNG, atau WebP.');
            }

            if ($foto->getSize() > 2 * 1024 * 1024) {
                throw MahasiswaException::uploadFotoGagal('Ukuran file maksimal 2MB.');
            }

            $path = $foto->store('mahasiswa/photos', 'public');

            if (!$path) {
                throw MahasiswaException::uploadFotoGagal('Tidak bisa menyimpan file.');
            }

            return $path;
        } catch (MahasiswaException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw MahasiswaException::uploadFotoGagal($e->getMessage());
        }
    }

    /**
     * Simpan log aktivitas ke file (File I/O)
     * Format: [TIMESTAMP] [ACTION] NIM - NAMA
     */
    private function logActivity(string $action, Mahasiswa $mahasiswa): void
    {
        try {
            $timestamp = now()->format('Y-m-d H:i:s');
            $userId = Auth::id() ?? 0;
            $logLine = "[{$timestamp}] [{$action}] User#{$userId} | NIM: {$mahasiswa->nim} | Nama: {$mahasiswa->nama}\n";

            $logPath = storage_path('logs/mahasiswa/activity-' . now()->format('Y-m-d') . '.log');
            $logDir = dirname($logPath);

            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            file_put_contents($logPath, $logLine, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            Log::warning('Gagal menulis activity log: ' . $e->getMessage());
        }
    }

    /**
     * Ambil semua data untuk algoritma (tanpa pagination)
     * Time Complexity: O(n) - full table scan
     */
    public function getAllForAlgorithm(): array
    {
        return Mahasiswa::select(
            'id',
            'nama',
            'nim',
            'jurusan',
            'fakultas',
            'email',
            'nomor_hp',
            'tanggal_lahir',
            'jenis_kelamin',
            'created_at'
        )->get()->toArray();
    }

    /**
     * Statistik dashboard
     * Time Complexity: O(n) - aggregate functions
     */
    public function getStatistik(): array
    {
        return [
            'total'        => Mahasiswa::count(),
            'laki_laki'    => Mahasiswa::where('jenis_kelamin', 'Laki-laki')->count(),
            'perempuan'    => Mahasiswa::where('jenis_kelamin', 'Perempuan')->count(),
            'per_jurusan'  => Mahasiswa::selectRaw('jurusan, COUNT(*) as total')
                ->groupBy('jurusan')
                ->orderBy('total', 'desc')
                ->get(),
            'per_fakultas' => Mahasiswa::selectRaw('fakultas, COUNT(*) as total')
                ->groupBy('fakultas')
                ->orderBy('total', 'desc')
                ->get(),
            'bulan_ini'    => Mahasiswa::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Pointer / reference PHP
     * Time Complexity: O(1)
     */
    private function normalizeData(array &$data): void
    {
        if (isset($data['nama'])) {
            $data['nama'] = strtoupper(trim($data['nama']));
        }

        if (isset($data['nim'])) {
            $data['nim'] = strtoupper(trim($data['nim']));
        }
    }

    /**
     * Polimorfisme: SearchInterface
     * Time Complexity: tergantung algoritma yang dipakai
     */
    public function searchData(SearchInterface $algorithm, string $keyword, string $field = 'nama'): array
    {
        return $algorithm->search(
            $this->getMahasiswaArray(),
            $keyword,
            $field
        );
    }

    /**
     * Export seluruh data mahasiswa ke file JSON
     * File I/O
     * Time Complexity: O(n)
     */
    public function exportToJson(): string
    {
        $data = $this->getMahasiswaArray();
        $fileName = 'exports/mahasiswa.json';

        Storage::disk('local')->put(
            $fileName,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return $fileName;
    }

    /**
     * Import data mahasiswa dari file JSON
     * File I/O
     * Time Complexity: O(n)
     */
    public function importFromJson(string $fileName): int
    {
        if (!Storage::disk('local')->exists($fileName)) {
            throw new MahasiswaException('File JSON tidak ditemukan.');
        }

        $content = Storage::disk('local')->get($fileName);
        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new MahasiswaException('Format JSON tidak valid.');
        }

        return DB::transaction(function () use ($data) {
            $count = 0;

            foreach ($data as $item) {
                if (!is_array($item) || empty($item['nim'])) {
                    continue;
                }

                $this->normalizeData($item);

                Mahasiswa::updateOrCreate(
                    ['nim' => $item['nim']],
                    $item
                );

                $count++;
            }

            return $count;
        });
    }

    /**
     * Mengambil seluruh mahasiswa sebagai array
     * Digunakan untuk kebutuhan algoritma Search & Sort
     * Time Complexity: O(n)
     */
    public function getMahasiswaArray(): array
    {
        return Mahasiswa::all()->toArray();
    }
}
