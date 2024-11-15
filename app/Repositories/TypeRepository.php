<?php

namespace App\Repositories;

use App\Models\Type;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class TypeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Type::class;
    }

}
