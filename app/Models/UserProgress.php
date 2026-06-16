<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'progress_status',
        'details',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
