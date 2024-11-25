<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'goal_id',
        'message'
    ];

    protected $dates = [ 'deleted_at' ];

    protected $casts = [
        'goal_id' => 'string',
        'message' => 'string'
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
