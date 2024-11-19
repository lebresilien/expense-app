<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

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

        $data = [
            'incomes' => $incomes,
            'totalIncomes' => $incomes->sum('amount'),
            'expenses' => $expenses,
            'totalExpenses' => $expenses->sum('amount'),
            'month' => Carbon::now()->firstOfMonth()->format('d M') . ' - ' . Carbon::now()->lastOfMonth()->format('d M Y')
        ];

        return $data;
    }

}
