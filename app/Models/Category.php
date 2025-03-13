<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'type_id',
        'user_id'
    ];

    protected $dates = [ 'deleted_at' ];

    protected $casts = [
        'name' => 'string',
        'type_id' => 'string',
        'user_id' => 'string',
        'created_at' => 'datetime:Y-m-d'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
