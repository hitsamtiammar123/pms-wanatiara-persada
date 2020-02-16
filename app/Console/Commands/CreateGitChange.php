<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateGitChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:git:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini berfungsi untuk melakukan perubahan berdasarkan file change.json di app storage folder';

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
        $contents=file_get_contents(storage_path('app/update.json'));
        $data_changes=json_decode($contents,true);

        foreach($data_changes['data'] as $data){
            $content=$data['content'];
            $file=$data['file'];
            $path=base_path($file);
            file_put_contents($path,$content);
            $this->info("$file sudah berhasil disimpan");
        }

    }
}
