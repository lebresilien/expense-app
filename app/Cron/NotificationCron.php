<?php
namespace App\Cron;

use App\Models\{ Notification, Goal };
use Carbon\Carbon;
use App\Repositories\NotificationRepository;

class NotificationCron
{
    public function __invoke()
    {
        Goal::whereStatus(true)->get()->map(function($item) {
            if(Carbon::parse(Carbon::now()->addDays(5)->format('Y-m-d'))->eq(Carbon::parse($item['expiredAt']))) {
                Notification::create([
                    'goal_id' => $item['id'],
                    'message' => 'Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum Lorem ipsum'
                ]);
            }
        });
    }
}
