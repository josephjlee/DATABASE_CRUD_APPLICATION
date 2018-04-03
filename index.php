<?php

// on inclus l'autoloader qui chargera automatiquement toutes nos pages créees
require_once 'core/init.php';

//echo Config::get('mysql/host'); // '127.0.0.1'


//$user = DB::getInstance()->query("SELECT * FROM users");
/*$user = DB::getInstance()->update('users', 3,  array(
    'password'  => 'newpassword',
    'name' => 'Zagalo Mendosa'
));
*/

$user = DB::getInstance()->get('users', array('username', '=', 'alex'));

if(!$user->count())
{
    echo 'no user';
} else 
    {
        echo $user->first()->username;
    }



    
