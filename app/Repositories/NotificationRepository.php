<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\BaseRepository;

class NotificationRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'goal_id',
        'message'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Notification::class;
    }

}
