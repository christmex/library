<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Flowframe\Trend\Trend;

use App\Models\Transaction;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaction Statistic';
    protected static string $color = 'info';
    protected int | string | array $columnSpan = 'full';

    // protected static ?array $options = [
    //     'plugins' => [
    //         'legend' => [
    //             'display' => false,
    //         ],
    //     ],
    //     'responsive' => true,
    // ];

    // public ?string $filter = 'today';

    // protected function getFilters(): ?array
    // {
    //     return [
    //         'today' => 'Today',
    //         'week' => 'Last week',
    //         'month' => 'Last month',
    //         'year' => 'This year',
    //     ];
    // }
    protected function getData(): array
    {
        // $activeFilter = $this->filter;

        // $trend = Trend::query(User::where('name', 'like', 'a%'))
        $data = Trend::model(Transaction::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
 
        return [
            'datasets' => [
                [
                    'label' => 'Transaction',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('F')),
        ];

        
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): ?string
    {
        return 'The number of transaction per month.';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'decimation' => [
                    // 'enabled' => false,
                    // 'algorithm' => 'lttb',
                ]
            ],
        ];
    }
    
}
