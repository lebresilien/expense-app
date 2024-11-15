<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class TransactionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'user_id',
        'type_id',
        'amount',
        'date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Transaction::class;
    }

}
