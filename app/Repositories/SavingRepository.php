<?php

namespace App\Repositories;

use App\Models\Saving;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class SavingRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'amount',
        'goal_id',
        'day'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Saving::class;
    }

}
