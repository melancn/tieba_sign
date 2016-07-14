<?php
function get_db_connect(){
    static $link = null;
    if(!is_object($link)){
        $config = include('config.php');

        try {
            $pdo = new PDO("mysql:host={$config['db']['host']};port={$config['db']['port']};dbname={$config['db']['dbname']}",$config['db']['user'],$config['db']['password']); 
            $pdo->query('set names '.$config['db']['charset']);
        } catch (PDOException $e) {var_dump($e->errorInfo);die;}
        $link = $pdo;
    }
    return $link;
}
$pdo = get_db_connect();