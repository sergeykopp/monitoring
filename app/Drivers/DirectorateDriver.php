<?php

namespace Kopp\Drivers;

use Kopp\Models\Directorate;

class DirectorateDriver
{
    // Удаление неактуальных дирекций, у которых нет связей с проблемами, навсегда
    public static function deleteNotActualDirectorates()
    {
        $directorates = Directorate::where('actual', false)->get();
        foreach($directorates as $directorate){
            if(0 === count($directorate->troubles) and 0 === count($directorate->filials)){
                $directorate->delete();
            }
        }
    }
}