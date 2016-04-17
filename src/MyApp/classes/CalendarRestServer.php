<?php

/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 11.04.2016
 * Time: 11:41
 */
class CalendarRestServer
{

    public $url;
    public $method;
    public $params;
    public $request;
    public $input;
    public $calendar;
    public $resources;
//    public $resource;
//    public $resourceId;
//    public $subResource;
    public $resultFormat;

    private static $errors = [
        404 => 'Page not found'
    ];

    public function __construct()
    {

    }

    public function loadCalendar()
    {
        $facade = new CalendarFacade();
        $this->calendar = $facade->getCalendar();
    }

    public function getParams()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, false);

        return $data;
    }

    public function getMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $override = isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : (isset($_GET['method']) ? $_GET['method'] : '');
        if ($method == 'POST' && strtoupper($override) == 'PUT') {
            $method = 'PUT';
        } elseif ($method == 'POST' && strtoupper($override) == 'DELETE') {
            $method = 'DELETE';
        }

//        var_dump($method);
        return $method;
    }

    public function getResultFormat()
    {
        $format = "";
        if (array_key_exists("xml", $_POST) || array_key_exists("xml", $_GET)) {
            $format = "xml";
        }
        if (array_key_exists("json", $_POST) || array_key_exists("json", $_GET)) {
            $format = "json";
        }

        return $format;
    }

    private function reportError($errorCode = 404)
    {

        http_response_code($errorCode);
        die(self::$errors[$errorCode]);
    }

    private function toJson($data, $code)
    {
        header("Content-Type: application/json");

        $result = [];
        http_response_code($code);
        if(!is_object($data)) {

            $data = new ArrayResource($data);

        }
        $result[get_class($data)] = $data;
        echo json_encode($result);

    }

    private function toXml($data, $code)
    {

        http_response_code($code);
        header("Content-type: xml");

        if(!is_object($data)){
            $data = new ArrayResource($data);
        }
        $xml = new SimpleXMLElement($this->generate_valid_xml_from_array($data, get_class($data), "item"));
        print $xml->asXML();
    }

    private function result($data, $code = 200)
    {
        switch ($this->resultFormat) {
            case "json":
                $this->toJson($data, $code);
                break;
            case "xml":
                $this->toXml($data, $code);
                break;
            default:
                $this->toXml($data, $code);
                break;
        }

//        dd($data);
    }


    function generate_xml_from_array($array, $node_name)
    {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }

                $xml .= '<' . $key . '>' . "\n" . $this->generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES) . "\n";
        }

        return $xml;
    }

    function generate_valid_xml_from_array($array, $node_block = 'nodes', $node_name = 'node')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

        $xml .= '<' . $node_block . '>' . "\n";
        $xml .= $this->generate_xml_from_array($array, $node_name);
        $xml .= '</' . $node_block . '>' . "\n";

        return $xml;
    }

    private function processResource($beforeResource = null, $resource, $resourceName = null)
    {

        switch ($this->method) {
            case "GET":

                $this->result($resource, 200);

                return;
            case "POST":
//                var_dump("asdfas");
                if(isset($_GET["apiFunction"])){
                    $function = $_GET["apiFunction"];

                    if(method_exists($resource, $function)){

                        $result = $resource->{$function}($this->params);
                        if($result){
                            $this->result($result, 200);
                        } else {
                            $this->reportError(404);
                        }

                    }
                }
                else
                if ($resource == null) {

                    $beforeResource->post($beforeResource, $resourceName, $this->params);
                    $this->result($beforeResource, 200);
                } else {
                    $resource->post(null, $resourceName, $this->params);
                    $this->result($beforeResource, 200);
                }

                return;
            case "PUT":
                if ($resource == null) {

                    $beforeResource->post($beforeResource, $resourceName, $this->params);
                    $this->result($beforeResource, 200);
                } else {
                    $resource->put($this->params);
                    $this->result($beforeResource, 200);
                }
//                $resource->put($this->params);
//                $this->result($resource, 200);

                return;
            case "DELETE":
                unset($resource);

//                $this->result($resource, 200);
                return;
            default:
                break;
        }

    }

    private function processRequest()
    {

        $finalResource = $this;
        $beforeResource = $this;
        $resourceName = "";
        while ($resource = array_shift($this->resources)) {
            if($resource[0] == "&") break;
//            var_dump($resource);
            $resourceName = $resource;
            if ($finalResource) {
                $beforeResource = $finalResource;
                if (!is_numeric($resource)) {
//                    var_dump(property_exists($finalResource, $resource));
                    if (property_exists($finalResource, $resource)) {

                        $finalResource = $finalResource->{$resource};

//                        var_dump("tu som");
                    }
                } else {
                    $finalResource = $finalResource->getByResourceId($resource);
                }
            }

        }
        if ($beforeResource || $resourceName) {

            $this->processResource($beforeResource, $finalResource, $resourceName);

            $this->saveState();

        } else {
            $this->reportError(404);
        }


    }

    private function saveState()
    {

        $filePath = getcwd() . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . session_id() . "-calendar.obj";

        $fp = fopen($filePath, "w");
        fwrite($fp, base64_encode(serialize($this->calendar)));
        fclose($fp);

        $_SESSION['loaded'] = true;

    }

    function multiexplode ($delimiters,$string) {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }

    public function handle()
    {


        $request_temp = explode('/', trim($_SERVER['REDIRECT_URL'], '/'));
//        $request = $this->multiexplode(['/', '&'], trim($_SERVER['REDIRECT_URL'], '/'));
        $request = [];
        foreach($request_temp as $r){
            if(strpos($r, "&") != false){
                $request[] = substr($r, 0, strpos($r, "&"));
            }
            else {
                $request[] = $r;
            }
        }
        $this->request = $request;

        array_shift($request);
        while (count($request) > 0) {
            $this->resources[] = array_shift($request);
        }
//        dd($this->resources);
//        $this->resource = array_shift($request);
//        if(isset($request[0]) && !is_numeric($request[0])){
//            $this->subResource = array_shift($request);
//        }
//        $this->resourceId = array_shift($request);
        $this->method = $this->getMethod();
        $this->params = $this->getParams();
//        dd($this->params);
        $this->resultFormat = $this->getResultFormat();

        $this->processRequest();


    }


}