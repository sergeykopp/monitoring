<?php

namespace Kopp\Drivers;

use Kopp\Models\Filial;

class FilialDriver
{
    // Удаление неактуальных филиалов, у которых нет связей с проблемами, навсегда
    public static function deleteNotActualFilials()
    {
        $filials = Filial::where('actual', false)->get();
        foreach($filials as $filial){
            if(0 === count($filial->troubles) and 0 === count($filial->cities)){
                $filial->delete();
            }
        }
    }
}