<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\BookStock;
use App\Models\Member;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total transaction', $this->getTotalTransaction())
                // ->description('32k increase')
                // ->descriptionIcon('heroicon-m-arrow-trending-up')
                ,
            Stat::make('Total member', $this->getTotalMember())
                // ->description('7% increase')
                // ->descriptionIcon('heroicon-m-arrow-trending-down')
                ,
            Stat::make('Total book by title', $this->getTotalBook())
                // ->description('3% increase')
                // ->descriptionIcon('heroicon-m-arrow-trending-up')
                ,
            Stat::make('Total book by stock', $this->getTotalBookByStock())
                // ->description('32k increase')
                // ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->chart([7, 2, 10, 3, 15, 4, 17])
                // ->color('success')
                ,
        ];
    }

    public function getTotalTransaction(){
        return Transaction::all()->count();
    }

    public function getTotalMember(){
        return Member::all()->count();
    }

    public function getTotalBook(){
        return Book::all()->count();
    }

    public function getTotalBookByStock(){
        // return Book::all()->sum('qty');
        return BookStock::all()->sum('qty');
    }
}
