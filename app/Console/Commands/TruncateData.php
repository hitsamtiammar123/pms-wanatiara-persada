<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Role;
use App\Model\Employee;
use DB;

class TruncateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:migrate:empty {table=?* : Nama Table. Jika tidak didefinisakn akan mengosongkan semuat table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ini Berfungsi untuk mengosongkan data dalam suatu table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

     protected $tables=['users','employees','roles','kpiheaders',
     'kpiresults','kpiendorsements','kpiprocesses'];
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
        $arg=$this->arguments();
        
        $table=@$arg['table='];

        if(count($table)!==0)
            $final_table=$table;
        else
            $final_table=$this->tables;
        //dd($final_table);

        for($i=0;$i<count($final_table);$i++){
            $t=$final_table[$i];
            if($t==='employees'){
                DB::update("UPDATE {$t} SET atasan_id=null");
            }
            $q="DELETE FROM {$t}";
            $result=DB::delete($q);
            $this->info("Jumlah data yang dihapus di table {$t}= {$result}");
        }
        // if(!$table){
        //     $q=DB::delete('DELETE FROM employees');
        //     $q2=DB::delete('DELETE FROM roles');

        //     $this->info("Jumlah data yang dihapus di table employees= {$q}, roles= {$q2}");
        // }
    }
}
