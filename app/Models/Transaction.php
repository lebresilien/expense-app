<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'amount',
        'date',
        'user_id',
        //'type_id',
        'category_id',
        'description'
    ];

    protected $dates = [ 'deleted_at' ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'amount' => 'double',
        'date' => 'date:Y-m-d',
        'user_id' => 'string',
        'category_id' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

}
