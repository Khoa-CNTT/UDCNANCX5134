<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $table = 'violations';

    protected $fillable = [
        'user_id',
        'plate_number',
        'plate_image',
        'image_url',
        'violation_time',
        'status',
    ];

    protected $casts = [
        'violation_time' => 'datetime',
    ];

    // Mối quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
