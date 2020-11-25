<?php

namespace Kopp\Drivers;

use Kopp\Models\City;

class CityDriver
{
    // Удаление неактуальных городов, у которых нет связей с проблемами, навсегда
    public static function deleteNotActualCities()
    {
        $cities = City::where('actual', false)->get();
        foreach($cities as $city){
            if(0 === count($city->troubles) and 0 === count($city->offices)){
                $city->delete();
            }
        }
    }
}