<?php session_start();

/* 
    PAGE DE CONFIGURATION  
    init.php contient l'autoloader qui inclus automatiquement toutes nos class créees
    cette page contient tous nos paramètres de connexion et de session qui pourront etre appelés depuis n'importe quelle page

    'mysql' contient les paramètres de connexion a la base de donnée [TOUJOURS PREFERER EN LOCAL 127.0.0.1 à localhost]
    'remember' définie les paramètre de création de cookies
    'session' définie les paramètres de session 
*/

$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'login_register'    
    ),
    'remember' =>array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),
    'session' => array(
        'session_name' => 'user'
    )
);



//on inclus dynamiquement nos Class du dossier classes
spl_autoload_register(function($class)
{
    require_once 'classes/' .$class. '.php';
});

    
//on inclus notre function sanitize (séparement car elle n'est pas une classe)
require_once 'functions/sanitize.php';
