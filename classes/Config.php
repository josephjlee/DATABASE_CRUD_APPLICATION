<?php

class Config
{
    /**
     * cette function paramètre selon l'utilisation les réglages de core/init.php
     *
     * @param $path
     * @return $config
     */
    public static function get($path = null)
    {
        if($path)
        {
            $config = $GLOBALS['config'];
            $path = explode('/', $path);

            foreach ($path as $bit)
            {
                if(isset($config[$bit]))
                {
                   $config = $config[$bit];
                }
            }
            
            return $config;
        }
    }
}



