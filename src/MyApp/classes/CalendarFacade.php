<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 11.04.2016
 * Time: 13:22
 */

class CalendarFacade {

    private static $calendar = null;

    public function __construct()
    {
//        if(!isset($_SESSION['calendar'])){


//        } else {
            $filePath = getcwd().DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR.session_id()."-calendar.obj";
//            dd(@file_get_contents($filePath));
            if (isset($_SESSION['loaded'])){
//                var_dump("tu som");
                $objData = file_get_contents($filePath);
                $obj = unserialize(base64_decode($objData));
                CalendarFacade::$calendar = $obj;
            } else {
                CalendarFacade::$calendar = Calendar::getInstance();
            }
//        }
//        CalendarFacade::$calendar = unserialize($_SESSION['calendar']);
    }

    public function getCalendar(){
        return CalendarFacade::$calendar;
    }


}