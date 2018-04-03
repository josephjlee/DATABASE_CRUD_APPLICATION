<?php

/*
    Cette page comme son nom l'indique sécurise toutes les données
    envoyées ou reçues des utilisateurs avant éventuellement l'enregistrement des informations dans la base de donnée.
*/



/**
 * cette fonction sécurise les données.
 * 
 * htmlentities - Convertit les guillemets doubles et les guillemets simples.
 * trim - Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
 * stripslashes — Supprime les antislashs d'une chaîne
 * htmlspecialchars - Convertit les caractères spéciaux en entités HTML
 * @param $string
 * @return $string nettoyé
 */
function escape($string)
{
    $string =  htmlentities($string, ENT_QUOTES, 'UTF-8');
    $string =  trim($string);
    $string =  stripslashes($string);
    $string =  htmlspecialchars($string);

    return $string;
}


