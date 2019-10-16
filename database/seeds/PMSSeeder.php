<?php

use Illuminate\Database\Seeder;

class PMSSeeder extends Seeder
{

    /**
     * Nama dari filesystem
     *
     * @var string
     */
    protected $storage='local';

    /**
     * Nama dari folder backup
     *
     * @var string
     *
     */
    protected $dir='backup';

    /**
     * Nama dari objek disk
     *
     * @var Illuminate\Filesystem\FilesystemAdapter
     */
    protected $disk;

    /**
     * Daftar dari tabel yang tidak punya relasi
     *
     * @var array
     */
    protected $d1tables=[
        'roles',
        'kpiresults',
        'kpiprocesses',
        'notifications',
        'password_resets'
    ];

    /**
     * Daftar dari tabel yang mempunyai 1 relasi
     *
     * @var array
     */
    protected $d2tables=[
        'employees',
        'users',
        'kpiheaders',
        'kpitags',
        'pmslogs'
    ];

    /**
     * Daftar dari tabel yang mempunyai 2 relasi atau tabel yang berelasi dengan tabel dgn 2 relasi
     *
     * @var array
     */
    protected $d3tables=[
        'kpiresultsheader',
        'kpiprocesses_kpiheaders',
        'kpiendorsements',
        'priviledgeresultskpia',
        'groupingkpi',
        'kpiprocessgroup',
        'kpiresultgroup'
    ];

    /**
     * Data sementara dari daftar tabel
     *
     * @var array
     */
    protected $buffers=[];

    /**
     * Mengembalikan nama file dari suatu backup table
     *
     * @param string $table nama dari suatu tabel
     * @return string
     */
    protected function getFile($table){
        return "$this->dir/$table.json";
    }

    /**
     * Melakukan filter untuk tabel tertentu
     *
     * @param array $data Data yang mau di-filter
     * @param string $type tipe/nama tabel dari data yang ingin di-filter
     * @return array
     */

    protected function filterParticularData(array $data ,$type){
        $this->buffers[$type]=$data;
        switch($type){
            case 'employees':
                $data=array_map(function($d){
                    $d['atasan_id']=null;
                    return $d;
                },$data);
            break;
        }
        return $data;
    }

    /**
     * berfungsi untuk melakukan migrasi pada tabel yang tidak mempunya relasi
     *
     * @return void
     */
    protected function migrateTable(){
        for($i=1;$i<=3;$i++){
            $dcurr="d{$i}tables";
            $darr=$this->{$dcurr};
            foreach($darr as $table){
                $file=$this->getFile($table);
                $rawdata=$this->disk->get($file);
                $data=json_decode($rawdata,true);
                $data=$this->filterParticularData($data,$table);
                \DB::table($table)->insert($data);
            }
        }

    }

    /**
     * berfungsi untuk melakukan mapping pada data employee yang sudah dimasukan sebelumnya
     *
     * @return void
     */
    protected function mapAtasan(){
        $table='employees';
        if(!array_key_exists($table,$this->buffers)){
            return;
        }
        $employees=$this->buffers[$table];
        $atasan_id='atasan_id';

        foreach($employees as $employee){
            if(!array_key_exists($atasan_id,$employee))
                continue;
            $curr_atasan_id=$employee[$atasan_id];
            $id=$employee['id'];
            $return=\DB::table($table)
            ->where('id',$id)
            ->update([$atasan_id => $curr_atasan_id]);

        }
    }

    /**
     *
     * Constructor untuk menset default property
     *
     * @return void
     */
    public function __construct($storage=null,$dir=null){
        $this->storage=!is_null($storage)?$storage:'local';
        $this->dir=!is_null($dir)?$dir:'backup';
        $this->disk=\Storage::disk($this->storage);

    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->migrateTable();
        $this->mapAtasan();
    }
}
