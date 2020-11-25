<?php

namespace Kopp\Drivers;

use Kopp\Models\Office;

class OfficeDriver
{
    // Удаление неактуальных подразделений, у которых нет связей с проблемами, навсегда
    public static function deleteNotActualOffices()
    {
        $offices = Office::where('actual', false)->get();
        foreach($offices as $office){
            if(0 === count($office->troubles)){
                $office->delete();
            }
        }
    }
}