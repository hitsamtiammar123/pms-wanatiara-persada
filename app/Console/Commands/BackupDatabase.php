<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:migrate:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini berfungsi untuk melakukan backup terhadap database yang ada';

    /**
     * Mendapatkan nama dari database yang digunakan
     *
     * @var string
     */
    protected $dbname;

    /**
     * Menentukan filesystem dimana data backup akan disimpan
     *
     * @var string
     */
    protected $storage='local';

    /**
     * Menentukan nama folder dimana data backup akan disimpan
     *
     * @var string
     */
    protected $dir='backup';

    /**
     *
     * Method ini berfungsi untuk mengembalikan semua nama table yang terdapat pada database
     *
     * @return array
     */
    protected function getAllTables(){
        $tables=\DB::select('SHOW TABLES');
        $var="Tables_in_$this->dbname";
        return array_map(function($table)use($var){
            return $table->{$var};
        },$tables);
    }

    /**
     * Melakukan proses Backup
     *
     * @return void
     */

    protected function doBackup(){
        $tables=$this->getAllTables();
        $backupfolder=date('YmdHis');
        $disk=\Storage::disk($this->storage);

        foreach($tables as $table){
            $fullfolder="$this->dir/$backupfolder";
            $data=\DB::table($table)->get();
            $serialize=json_encode($data,JSON_UNESCAPED_SLASHES);
            $filename="$fullfolder/$table.json";
            $secondfile="$this->dir/$table.json";
            $disk->put($filename,$serialize);
            $disk->put($secondfile,$serialize);
            $this->info("Data dari tabel $table sudah dimasukan kedalam berkas \"$filename\"");
        }
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dbname= env('DB_DATABASE', 'forge');
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->doBackup();
    }
}
