<?php
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 11.04.2016
 * Time: 13:27
 */
class Calendar extends Resource
{
    public $items = [];

    public function __construct()
    {
        $this->items = [];
    }

    public static function getInstance(){

        $calendar = new Calendar();
        $calendar->loadData();
        return $calendar;
    }

    public function getByResourceId($resourceId){
        return $this->getByDate(['den' => Carbon::createFromFormat("md", $resourceId)->format('d.m.Y')]);

    }

    public function getByDate($params = []){
        $params = (array) $params;
        if(isset($params['den'])){
            $den = Carbon::createFromFormat("d.m.Y", $params['den']);
            foreach($this->items as $element){
                if(Carbon::parse($element->den)->isSameDay($den)){
                    return $element;
                }
            }
        }

        return null;
    }

    public function getDateByNameAndCountry($params = []){
        $params = (array) $params;
        if(isset($params['name']) && isset($params['country'])){
            $name = $params['name'];
            $countryCode = $params['country'];

            foreach($this->items as $element){
                if(property_exists(CalendarName::class, $countryCode)
                    && $element->{$countryCode} != null
                    && in_array($name, $element->{$countryCode}->items)
                ){
                    return $element;
                }
            }
        }

        return null;
    }

    public function getHolidaysByCountry($params = []){
        $result = null;
        $params = (array) $params;
        if(isset($params['country'])) {
            $result = [];
            $countryCode = $params['country'];
            $propertyName = $countryCode . "sviatky";
            foreach ($this->items as $element) {
                if (property_exists(CalendarName::class, $propertyName)
                    && $element->{$propertyName} != null
                ) {
                    $result[] = ["den" => $element->den, $propertyName => $element->{$propertyName}];
                }
            }
        }
        return $result;
    }
    public function getMemoryDays($params = []){
        $result = null;
        $params = (array) $params;
        if(isset($params['country'])) {
            $countryCode = $params['country'];
            $propertyName = $countryCode . "dni";
            foreach ($this->items as $element) {
                if (property_exists(CalendarName::class, $propertyName)
                    && $element->{$countryCode . "dni"} != null
                ) {
                    $result[] = ["den" => $element->den, $propertyName => $element->{$propertyName}];
                }
            }
        }
        return $result;
    }

    private function loadData(){
        $xml = simplexml_load_file("data/meniny.xml");
        foreach($xml->zaznam as $element){
            $this->items[] = CalendarName::create($element);
        }
    }

    public function post($resource, $resourceName, $params){
        http_response_code(405);
        die("Post metoda pre resource calendar nie je podporovana");
    }
}