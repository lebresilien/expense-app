<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'amount',
        'expiredAt',
        'user_id'
    ];

    protected $dates = [ 'deleted_at' ];

    protected $casts = [
        'name' => 'string',
        'amount' => 'double',
        'expiredAt' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savings()
    {
       return $this->hasMany(Saving::class);
    }
}
