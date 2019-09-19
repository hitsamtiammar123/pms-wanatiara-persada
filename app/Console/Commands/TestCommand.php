<?php

namespace App\Console\Commands;

use App\Model\Employee;
use App\Model\KPIEndorsement;
use App\Model\KPIHeader;
use App\Model\KPIResultHeader;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hehe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ini command Heheeheeheh';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id=KPIEndorsement::generateID('1915284117');
        printf($id);
    }
}
