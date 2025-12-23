<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ClassRoom (Kelas).
 * 
 * Menggunakan nama ClassRoom karena 'Class' adalah reserved word di PHP.
 */
class ClassRoom extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'program_type',
        'taud_level',
        'jilid_level',
        'academic_year',
        'teacher_id',
        'capacity',
        'schedule',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'jilid_level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTpq($query)
    {
        return $query->where('program_type', 'tpq');
    }

    public function scopeTaud($query)
    {
        return $query->where('program_type', 'taud');
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getAvailableSlotsAttribute(): int
    {
        return $this->capacity - $this->student_count;
    }
}
