<?php

/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 11.04.2016
 * Time: 20:42
 */
abstract class Resource
{
    private $cast = [];
    public $items = [];

    public function get(){
        return $this->items;
    }

    public function post($resource, $resourceName, $params){
        http_response_code(405);
    }

    public function delete(){
        $this->items = null;
    }
//    public function put($params){
//        if($params->value) {
//            $this->items = explode(',', str_replace(" ", "", $params->value));
//        }
//    }

    public function getByResourceId($resourceId){

        if(isset($this->items[$resourceId])){
            return $this->items[$resourceId];
        }
        return null;
    }

    public function postToResource($resourceName, $value){
        if(array_key_exists($resourceName, $this->cast)){
            $this->{$resourceName}[] = $value;
            return $this;
        }
        return false;
    }

    public function deleteFromResource($resourceName, $index){
        if(array_key_exists($resourceName, $this->cast)){
            if(isset($this->{$resourceName}[$index])){
                unset($this->{$resourceName}[$index]);
                return $this;
            }
        }
        return false;
    }
}