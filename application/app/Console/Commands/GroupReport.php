<?php

namespace App\Console\Commands;

use App\Models\WB\WbIncome;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GroupReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $incomeIds = WbIncome::query()->select('srid')->distinct()->count();

        dd($incomeIds);

        return CommandAlias::SUCCESS;
    }
}
