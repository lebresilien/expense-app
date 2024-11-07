<?php

namespace App\Repositories;

use App\Models\Goal;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class GoalRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'amount'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Goal::class;
    }

}
