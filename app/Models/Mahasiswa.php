<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Mahasiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'nama',
        'nim',
        'jurusan',
        'fakultas',
        'email',
        'nomor_hp',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function validateNim(string $nim): bool
    {
        // Konsisten dengan aturan: 7-15 digit angka
        return (bool) preg_match('/^[0-9]{7,15}$/', $nim);
    }

    public static function validateNomorHp(string $nomor): bool
    {
        // Terima format +62..., 62..., atau 08..., dan panjang wajar
        return (bool) preg_match('/^(?:\+62|62|0)8[0-9]{6,12}$/', $nomor);
    }

    public function scopeSearch(Builder $query, string $term)
    {
        $term = trim($term);
        return $query->where(function ($q) use ($term) {
            $q->where('nama', 'like', "%{$term}%")
              ->orWhere('nim', 'like', "%{$term}%")
              ->orWhere('jurusan', 'like', "%{$term}%");
        });
    }

    public function scopeByJurusan(Builder $query, string $jurusan)
    {
    
        return $query->where('jurusan', $jurusan);
    }

    public function toExportArray(): array
    {
    return [
        'NIM' => $this->nim,
        'Nama' => $this->nama,
        'Jurusan' => $this->jurusan,
        'Fakultas' => $this->fakultas,
        'Email' => $this->email,
        'Nomor HP' => $this->nomor_hp,
        'Alamat' => $this->alamat,
        'Tanggal Lahir' => $this->tanggal_lahir
            ? $this->tanggal_lahir->format('d/m/Y')
            : '',
        'Jenis Kelamin' => $this->jenis_kelamin,
        'Dibuat' => $this->created_at
            ? $this->created_at->format('d/m/Y H:i')
            : '',
    ];
    }
}
