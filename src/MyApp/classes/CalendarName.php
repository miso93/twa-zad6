<?php
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 11.04.2016
 * Time: 13:12
 */
class CalendarName extends Resource
{

    public $den;
    public $SKd;
    public $SKsviatky;
    public $CZsviatky;
    public $SK;
    public $CZ;
    public $HU;
    public $PL;
    public $AT;

    public $SKdni;


    protected static $dates = ['den'];
    protected static $dateFormat = "md";

    private $cast = [
        "SKd"       => "array",
        "SK"        => "array",
        "CZ"        => "array",
        "HU"        => "array",
        "PL"        => "array",
        "AT"        => "array",
        "SKsviatky" => "array",
        "CZsviatky" => "array",
        "SKdni"     => "array"
    ];

    public function __construct()
    {
        foreach ($this->cast as $key => $value) {
            $this->{$key} = new ArrayResource();
        }
    }

//    public function get()
//    {
//        return get_object_vars ($this);
//
//    }
    public function post($resource, $resourceName, $params)
    {
//        dd($resource);
        $this->{$resourceName} =
            new ArrayResource(explode(',', str_replace(' ', '', $params->{$resourceName})));
    }

    public static function create($arr_data)
    {
        $arr_data = (array)$arr_data;
        $calendar_name = new CalendarName;
        foreach ($arr_data as $key => $value) {
            if (property_exists(CalendarName::class, $key)) {
                if (in_array($key, CalendarName::$dates)) {
                    $calendar_name->{$key} = Carbon::createFromFormat(CalendarName::$dateFormat, $value);
                } else {
                    if (array_key_exists($key, $calendar_name->cast)) {
                        switch ($calendar_name->cast[$key]) {
                            case "array":
                                $calendar_name->{$key} = new ArrayResource(explode(',', str_replace(' ', '', $value)));
                                break;
                            default:
                                break;
                        }
                    } else {
                        $calendar_name->{$key} = $value;
                    }

                }
            }
        }

        return $calendar_name;
    }

}