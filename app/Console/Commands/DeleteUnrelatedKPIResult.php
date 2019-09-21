<?php

namespace App\Console\Commands;

use App\Model\KPIResult;
use Illuminate\Console\Command;

class DeleteUnrelatedKPIResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:kpiresult:delete-unrelated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini berfungsi untuk menghapus data KPIResult yang tidak memiliki relasi';

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
        //
        $count=KPIResult::deleteUnrelatesData();
        $this->info("Jumlah data yang dihapus: $count");
    }
}
