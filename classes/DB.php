<?php

/**
 * par convention $_propety = propriété privée && $property = propriété public
 * 
 * CLass permettant d'intéragir avec la base de donnée.
 * $_instance représente l'instance de la class DB elle même (new DB()) qui exécutera son constructeur afin de se connecter UNE SEULE FOIS à la base de donnée.
 * $_pdo represente l'instance de l'objet PDO qui se connectera à la base de donnée
 * $_query -> dernière requête qui a été exécutée (PDOStatement)
 * $_error -> éventuel erreur, exemple le cas où query contient une erreur
 * $_results -> contient les résultats de la requête $_query
 * $_count -> Retourne le nombre de lignes affectées par la requête _query->rowCount()
 */
class DB
{
    private static $_instance = null;
    private $_pdo,
            $_query,
            $_error = false,
            $_results,
            $_count = 0; 


    /**
     * Le constructeur fait appel à la fonction static get de Config.php
     */
    private function __construct()
    {
        try
        {
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), 
                                    Config::get('mysql/username'), Config::get('mysql/password'));
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_pdo->exec("SET NAMES 'UTF8'");
           
        } catch(PDOException $e)
            {
                $this->_pdo = null;
                echo 'Échec lors de la connexion : ' . $e->getMessage();
            }
        
        return $this->_pdo;
    }        

    /**
     * cette fonction retourne l'instance de DB elle meme qui exécutera son constructeur 
     * afin de retourner $_pdo qui est l'instance de l'objet PDO qui sera enregistrée dans $_instance
     * on optimise ainsi les performances avec une et une seule connexion 
     *
     * @return $_instance représente l'instance de la class DB elle même (new DB()) 
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance))
        {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }



    public function query($sql, $params = array())
    {
        /*on redefinit _error à false car on peut etre amener à effectuer plusieurs requêtes*/
        $this->_error = false;
        if($this->_query = $this->_pdo->prepare($sql))
        {
            $x = 1;
            if(count($params))
            {
                foreach ($params as $param)
                {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }

            if($this->_query->execute())
            {
                /*PDO::FETCH_OBJ car on boss en objet et ça n'a aucun sens de retourner un array*/
                //$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $queryFunction = explode(" ",$sql);
                if($queryFunction[0] == "SELECT")
                {
			    	$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
				}
                $this->_count = $this->_query->rowCount();

            } else 
                {
                    $this->_error = true;
                }
        }
        return $this;
    }

    

    /**
     * function facilitant les function get() et delete()
     *
     * @param $action représente l'action majeur a éffectué dans la base de donnée utilisé dans get() et delete()"INSERT ...DELETE"
     * @param $table est le nom de la table de la base de donnée
     * @param array $where un array englobant la 1-la colomne de la base de donnée
     * 2- le signe (l'orérateur) 3- la valeur souhaitée
     * @return $this l'objet s'il n'ya pas d'erreur ou false si erreur
     */
    public function action($action, $table, $where = array())
    {
        if(count($where) === 3)
        {
            /* on peut bien evidemment en rajouter pour usage personnel*/
            $operators = array('=', '>', '<', '>=', '<=');

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if(in_array($operator, $operators))
            {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                if(!$this->query($sql, array($value))->error()) /* On appelle query() definit plus haut*/
                {
                    /* s'il n'y a pas d'erreur on retourne l'objet */
                    return $this;
                }
            }
        }

        return false;
    }
    

    /**
     * cette fonction appelle action() qui elle meme appelle query() à savoir un SELECT
     *
     * @param $table represente le nom de la table
     * @param $where est définit dans action()
     * @return voir action()
     */
    public function get($table, $where)
    {
        return $this->action('SELECT *', $table, $where);
    }

    public function delete($table, $where)
    {
        return $this->action('DELETE', $table, $where);
    }



    /**
     * Insère des éléments dans la base de donnée
     *
     * @param $table nom de la table
     * @param array $fields tableau dans $keys represente les clé du tableau et aussi le nom de la colonne en base de donnée
     * chaque valeur de $fiels est remplacé par un '?, ' afin de permettre la requête préparée
     * @return void
     */
    public function insert($table, $fields = array())
    {
        if(count($fields))
        {
            $keys = array_keys($fields); /*Retourne les clés du tableau qui representent les champs de la database*/
            $value = '';
            $x = 1;

            foreach($fields as $field)
            {
                $value .= '?';

                if($x < count($fields))
                {
                    $value .= ', ';
                }
                $x++;
            }
            
            $sql = "INSERT INTO users (`" . implode('`, `', $keys) . "`) VALUES ({$value})";
            
            if(!$this->query($sql, $fields)->error())
            {
                return true;
            }
        }
        return false;
    }


    public function update($table, $id ,$fields)
    {
        $set = '';
        $x = 1;

        foreach($fields as $name => $value)
        {
            $set .= "{$name} = ?";
            if($x < count($fields))
            {
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

        if(!$this->query($sql, $fields)->error())/* s'il n'y a pas d'erreur*/
        {
            return true;
        }

        return false;
    }




    /**
     * Retourne le résultat des requêtes enregistré dans query() 
     *
     * @return $this->_results;
     */
    public function results()
    {
        return $this->_results;
    }


    /**
     * Retourne le 1er résultat d'une requête
     *
     * @return $this->results()[0];
     */
    public function first()
    {
        return $this->results()[0];
    }

    /**
     * Retourne une éventuelle érreur rencontrée lors d'une requête qui est un booleen
     *
     * @return $_error == booleean
     */
    public function error()
    {
        return $this->_error;
    }

    /**
     * Retourne le nombre de lignes affectées par la requête _query->rowCount() dans query()
     *
     * @return $count
     */
    public function count()
    {
        return $this->_count;
    }
    

}