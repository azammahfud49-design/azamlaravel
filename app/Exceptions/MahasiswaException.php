<?php

namespace App\Exceptions;

use Exception;

class MahasiswaException extends Exception
{
    public static function nimSudahAda(string $nim): self
    {
        return new self("NIM '{$nim}' sudah terdaftar.", 409);
    }

    public static function mahasiswaTidakDitemukan(int $id): self
    {
        return new self("Mahasiswa dengan ID {$id} tidak ditemukan.", 404);
    }

    public static function formatTidakValid(string $field, string $keterangan): self
    {
        return new self("Format {$field} tidak valid. {$keterangan}", 422);
    }

    public static function uploadFotoGagal(string $alasan): self
    {
        return new self("Gagal upload foto: {$alasan}", 422);
    }

    public static function fileTidakDitemukan(string $path): self
    {
        return new self("File tidak ditemukan: {$path}", 404);
    }

    public static function dataKosong(): self
    {
        return new self('Tidak ada data mahasiswa.', 404);
    }
}
