<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ArrayImport implements ToCollection,WithHeadingRow
{


    public function headingRow(): int
    {
        return 12;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        dd($collection->toArray());
    }
}
