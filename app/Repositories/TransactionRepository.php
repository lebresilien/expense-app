<?php

namespace App\Repositories;

use App\Models\{ Transaction, Type };
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

    public function list($user_id, $start = null, $end = null) {

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        $formattedStartDate = $startDate->format('Y-m-d');
        $formattedEndDate = $endDate->format('Y-m-d');

        $type_income = Type::find(1)->categories->pluck('id');
        $type_expense = Type::find(2)->categories->pluck('id');

        $incomes = Transaction::where('user_id', $user_id)
                                ->whereIn('category_id', $type_income)
                                ->whereBetween('date', [ $start ? $formattedStartDate : Carbon::now()->firstOfMonth(), $start ? $formattedEndDate : Carbon::now()->lastOfMonth()])
                                ->get();

        $expenses = Transaction::where('user_id', $user_id)
                                ->whereIn('category_id', $type_expense)
                                ->whereBetween('date', [ $start ? $formattedStartDate : Carbon::now()->firstOfMonth(), $start ? $formattedEndDate : Carbon::now()->lastOfMonth()])
                                ->get();

        $months = Transaction::select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'))
                            ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
                            ->get()->map((function($item) {
                                return [
                                    'startMonth' => Carbon::create($item['year'], $item['month'])->firstOfMonth()->format('d M Y'),
                                    'endMonth' => Carbon::create($item['year'], $item['month'])->lastOfMonth()->format('d M Y')
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
