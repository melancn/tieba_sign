<?php
session_start();
include('config.php');
if(!$config['login']){
    header("Location:index.php");
    exit();
}

//ע����¼
if(isset($_GET['action']) && $_GET['action'] == "logout"){
	unset($_SESSION['uid']);
	header("Location:index.php");
	exit();
}

if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST'){
	header("Location:index.php");
	exit('�Ƿ�����!');
}

include('connect.php');

$pre = $this->db->prepare("SELECT * FROM tieba_sign_user where username=:username and status=1");
$pre->bindParam(':username',$_POST['username']);
$pre->execute();
$errorInfo = $pre->errorInfo();
if($errorInfo[0] != 0){
    $_SESSION['msg'] = $errorInfo[2];
}
$res = $pre->fetch();
if(!empty($res)){
    $pass = strtolower(sha1($res['salt'].$_POST['password']));
    if(!strcmp($pass,$res['password'])){
        $_SESSION['uid'] = $res['uid'];
        header("Location: site.php");
        exit();
    }
}
$_SESSION['msg'] = '�û��������������';
header("Location:index.php");
