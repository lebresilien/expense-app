<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use DB;

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

    public function list($user_id, $start, $end) {


        $incomes = Transaction::where('user_id', $user_id)
                                ->where('type_id', 1)
                                ->whereBetween('date', [ $start ? $start : Carbon::now()->firstOfMonth(), $start ? $start : Carbon::now()->lastOfMonth()])
                                ->get();

        $expenses = Transaction::where('user_id', $user_id)
                                ->where('type_id', 2)
                                ->whereBetween('date', [ $start ? $start : Carbon::now()->firstOfMonth(), $start ? $start : Carbon::now()->lastOfMonth()])
                                ->get();

        $months = Transaction::select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'))
                            ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
                            ->get()->map((function($item) {
                                return [
                                    'startMonth' => Carbon::create($item['year'], $item['month'])->firstOfMonth()->toDateString(),
                                    'endMonth' => Carbon::create($item['year'], $item['month'])->lastOfMonth()->toDateString()
                                ];
                            }));

        $data = [
            'incomes' => $incomes,
            'totalIncomes' => $incomes->sum('amount'),
            'expenses' => $expenses,
            'totalExpenses' => $expenses->sum('amount'),
            'startMonth' => Carbon::now()->firstOfMonth()->format('d M Y'),
            'endMonth' =>  Carbon::now()->lastOfMonth()->format('d M Y'),
            'months' => $months
        ];

        return $data;
    }

}
