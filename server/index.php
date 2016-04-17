<?php
require_once "../function.php";


//$xml = simplexml_load_file('../data/meniny.xml');
//
//$data = file_get_contents('php://input');
//$data = json_decode($data, false);
//var_dump($data);
//

//$reference = "/VS0020160127/SSNULL/KS0308";
//
//$reference = explode("/",$reference);
//if(isset($reference[1])){
//    $var_symbol = $reference[1];
//    $var_symbol = str_replace("VS", "", $var_symbol);
//    if(strpos($var_symbol, "20160127") !== false){
//        echo "match";
//    } else {
//        echo "doesnt match";
//    }
//}
//die();



$server = new CalendarRestServer();
$server->loadCalendar();
$server->handle();

//dd($server);
//
//$facade = new CalendarFacade();
//$calendar = $facade->getCalendar();



//$name = "Michal";
//$country = "CZ";
//$element = $calendar->getDateByNameAndCountry($name, $country);

//$element = $calendar->getByDate(\Carbon\Carbon::now()->firstOfYear());
//$element->SKsviatky = "ahoj20";
////
//
//var_dump(!isset($_SESSION['calendar']));
//
//$_SESSION['calendar'] = serialize($calendar);

//$calendar = $facade->getCalendar();

//
//var_dump(!isset($_SESSION['calendar']));
//
//$calendar = unserialize($_SESSION['calendar']);
//
//dd($calendar);


//dd($calendar);
//$element = $calendar->getMemoryDays($country);
//
//dd($element);
//dd(Calendar::getInstance()->getData());
