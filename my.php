<?php
session_start();
$config = include('config.php');

if(!isset($_SESSION['username']) && !empty($config['login']['username'])){
	header("Location:index.php");
	exit();
}