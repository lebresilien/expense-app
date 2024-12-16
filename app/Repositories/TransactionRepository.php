<?php

namespace App\Repositories;

use App\Models\{ Transaction, Type, Category };
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

    public function list($user_id, $start = null, $end = null, $type = null, $date = null) {

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        $formattedStartDate = $startDate->format('Y-m-d');
        $formattedEndDate = $endDate->format('Y-m-d');

        $type_income = Type::find(2)->categories->pluck('id');
        $type_expense = Type::find(1)->categories->pluck('id');

        if(!$type) {

            $incomes = Transaction::where('user_id', $user_id)
                                    ->whereIn('category_id', $type_income)
                                    ->whereBetween('date', [ $start ? $formattedStartDate : Carbon::now()->firstOfMonth(), $start ? $formattedEndDate : Carbon::now()->lastOfMonth()])
                                    ->get();

            $expenses = Transaction::where('user_id', $user_id)
                                    ->whereIn('category_id', $type_expense)
                                    ->whereBetween('date', [ $start ? $formattedStartDate : Carbon::now()->firstOfMonth(), $start ? $formattedEndDate : Carbon::now()->lastOfMonth()])
                                    ->get();

            $months = Transaction::select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'))
                                ->where('user_id', $user_id)
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
                'endMonth' => Carbon::now()->lastOfMonth()->format('d M Y'),
                'months' => $months
            ];
        } else {

            $array_data_month = collect([]);
            $array_data_year = collect([]);

            $months = Transaction::select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'))
                                ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
                                ->get()->map((function($item) {
                                    return
                                        Carbon::create($item['year'], $item['month'])->firstOfMonth()->format('M Y');
                                }));

            $years = Transaction::select(DB::raw('YEAR(date) as year'))
                        ->groupBy(DB::raw('YEAR(date)'))
                        ->get()->pluck('year');

            Category::where('type_id', 1)->with(['transactions' => function($query) use ($date) {
                $query->whereMonth('date', $date ? explode($date)[0] : Carbon::now()->format('m'))
                        ->whereYear('date', $date ? explode($date)[1] : Carbon::now()->format('Y'));
            }])->get()->map(function($item) use ($array_data_month) {
                if($item->transactions->sum('amount') > 0) {
                    $array_data_month->push([
                        'name' => $item->name,
                        'amount' => $item->transactions->sum('amount'),
                        'color' => 'red',
                        'legendFontColor' => '#7F7F7F',
                        'legendFontSize' => 15
                    ]);
                }
            });

            $stat_expenses_month = Transaction::whereIn('category_id', $type_expense)
                                ->whereMonth('date', $date ? explode($date)[0] : Carbon::now()->format('m'))
                                ->whereYear('date', $date ? explode($date)[1] : Carbon::now()->format('Y'))
                                ->get()->sum('amount');

            $stat_incomes_month = Transaction::whereIn('category_id', $type_income)
                                ->whereMonth('date', $date ? explode($date)[0] : Carbon::now()->format('m'))
                                ->whereYear('date', $date ? explode($date)[1] : Carbon::now()->format('Y'))
                                ->get()->sum('amount');

            Category::where('type_id', 1)->with(['transactions' => function($query) use ($date) {
                $query->whereYear('date', $date ? $date : Carbon::now()->format('Y'));
            }])->get()->map(function($item) use ($array_data_year) {
                if($item->transactions->sum('amount') > 0) {
                    $array_data_year->push([
                        'name' => $item->name,
                        'amount' => $item->transactions->sum('amount'),
                        'color' => 'red',
                        'legendFontColor' => '#7F7F7F',
                        'legendFontSize' => 15
                    ]);
                }
            });

            $stat_expenses_year = Transaction::whereIn('category_id', $type_expense)
                                ->whereYear('date', $date ? explode($date)[1] : Carbon::now()->format('Y'))
                                ->get()->sum('amount');

            $stat_incomes_year = Transaction::whereIn('category_id', $type_income)
                                ->whereYear('date', $date ? $date : Carbon::now()->format('Y'))
                                ->get()->sum('amount');

            $data = [
                'months' => $months,
                'years' => $years,
                'pie_month' => $array_data_month,
                'pie_year' => $array_data_year,
                'total_expense_month' => $stat_expenses_month,
                'total_expense_year' => $stat_expenses_year,
                'total_income_month' => $stat_incomes_month,
                'total_income_year' => $stat_incomes_year,
                'most_expense' => $array_data_month->sortByDesc('amount')->values()->all(),
                'most_expense_year' => $array_data_year->sortByDesc('amount')->values()->all()
            ];

        }

        return $data;
    }

}
