<?php
session_start();
$config = include('config.php');

if($config['login'] && empty($_SESSION['uid'])){
	header("Location:index.php");
	exit();
}