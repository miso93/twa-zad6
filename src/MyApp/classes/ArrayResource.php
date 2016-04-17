<?php

class ArrayResource extends Resource {

    public $items = [];

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public function post($resource, $resourceName, $params){
        if($params->value){
            $this->items = array_merge($this->items, explode(',', str_replace(" ", "", $params->value)));
            return $this;
        }
    }

    public function put($params){
//        dd($params);
        if($params->value) {
            $this->items = explode(',', str_replace(" ", "", $params->value));
            return $this;
        }
    }

//    public function put($params = []){
//        if(isset($params['value'])){
//            $this->items = explode(',', $params['value']);
//        }
//    }
}