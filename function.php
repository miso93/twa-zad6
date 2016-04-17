<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 08.03.2016
 * Time: 22:07
 */
if (session_id() == "") {
    session_start();
}

require_once "config.php";
require __DIR__ . '/vendor/autoload.php';

dibi::connect([
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'username' => Config::get('mysql.user'),
    'password' => Config::get('mysql.pass'),
    'database' => Config::get('mysql.db_name'),
    'charset'  => Config::get('mysql.charset'),
]);

function dd($arr)
{

    ?>
    <pre>
    <?php
    print_r($arr);
    ?>
    </pre>
    <?php
    die();
}
