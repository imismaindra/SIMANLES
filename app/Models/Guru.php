<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Guru extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'no_telp',
        'no_pegawai',
        'tanggal_lahir',
        'alamat',
        'jenjang',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cabang(): BelongsToMany
    {
        return $this->belongsToMany(Cabang::class, 'guru_cabang', 'guru_id', 'cabang_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'guru_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'guru_id');
    }

    /**
     * Daftar kualifikasi jenjang yang bisa diajar guru ini.
     *
     * @return list<string>
     */
    public function getJenjangListAttribute(): array
    {
        $rows = DB::table('guru_jenjang')->where('guru_id', $this->id)->pluck('jenjang')->all();

        return ! empty($rows) ? $rows : [$this->jenjang];
    }

    /**
     * Hitung siswa aktif yang sedang dibimbing oleh guru ini (semua cabang).
     */
    public function getActiveStudentsCountAttribute(): int
    {
        return $this->siswa()
            ->whereNull('deleted_at')
            ->where('status', 'aktif')
            ->count();
    }

    /**
     * Sisa slot bimbingan (Maksimal 6 siswa aktif).
     */
    public function getRemainingSlotsAttribute(): int
    {
        return max(0, 6 - $this->active_students_count);
    }

    /**
     * Periksa apakah guru memiliki kualifikasi untuk jenjang tertentu.
     */
    public function canTeachJenjang(string $jenjang): bool
    {
        return in_array($jenjang, $this->jenjang_list, true);
    }
}
