<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name'
    ];

    protected $dates = [ 'deleted_at' ];

    protected $casts = [
        'name' => 'string',
        'created_at' => 'datetime:Y-m-d',
    ];

    public function categories(): HasMany
    {
        return $this->belongsTo(Category::class);
    }
}
