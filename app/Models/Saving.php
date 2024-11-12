<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saving extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'amount',
        'goal_id',
        'day'
    ];

    protected $dates = [ 'deleted_at' ];

    protected $casts = [
        'amount' => 'double',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
