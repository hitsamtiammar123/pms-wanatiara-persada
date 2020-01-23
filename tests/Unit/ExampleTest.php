<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Model\KPIResultHeader;
use App\Model\KPITag;
use App\Model\KPIHeader;

class ExampleTest extends TestCase
{

    //use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $this->assertTrue(false);
    }

    public function testComparingStrName(){
        $str='冶炼厂产量、品位Volume Produksi dan Kadar Smelter plant ';
        $r=str_name_compare($str,'冶炼厂产量、品位Volume Produksi dan Kadar Smelter plant ');

        $this->assertTrue($r);

    }

    public function testSyncKPIHeader($period){
        $headers=KPIHeader::where('period','2019-10-16')->get();
        foreach($headers as $header){
            $curr_header=$header->getPrev();
            $header->kpiresultheaders->count()==!0?:$header->makeKPIResult($curr_header);
            $header->kpiprocesses->count()==!0?:$header->makeKPIProcess($curr_header);
            printf("Header dari %s untuk period %s sudah dibuat KPInya\n",$header->employee->name,$period);
            sleep(1);
        }
    }
}
