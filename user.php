<?php
include('my.php');
include('function.php');
include('connect.php');

if(!isset($_POST["type"])){
	echo '{"error":"缺少参数"}';
	exit();
}
$cn=0;
if($_POST["type"]=="show"){
	$a=array();
    $pre = $pdo->prepare("SELECT cid,uname,status FROM tieba_sign_cookies where uid=:uid and status >=0");
    $pre->bindParam(':uid',$_SESSION['uid']);
    $pre->execute();
    $errorInfo = $pre->errorInfo();
    if($errorInfo[0] != 0){
        $_SESSION['msg'] = $errorInfo[2];
        header("Location:index.php");
        exit;
    }
    while($r = $pre->fetch()){
		$a[] = $r;
	}
	echo json_encode($a);
}elseif($_POST["type"]=="new"){
	$cookie=$_POST["cookie"];
	$name=$_POST["name"];
    $pre = $pdo->prepare("SELECT * FROM tieba_sign_cookies WHERE status >=0 and uid=:uid and uname=:name ");
    $pre->bindParam(':uid',$_SESSION['uid']);
    $pre->bindParam(':name',$name);
    $pre->execute();
	if($pre->fetch()){
		echo '{"msg":"代号重复","no":1}';
		exit();
	}
	if($cn=testcookie($cookie)){
        $pre = $pdo->prepare("INSERT INTO tieba_sign_cookies (uid,uname,cookie,forum_num) VALUES (:uid,:name,:cookie,:forum_num)");
        $pre->bindParam(':uid',$_SESSION['uid']);
        $pre->bindParam(':name',$name);
        $pre->bindParam(':cookie',$cookie);
        $pre->bindParam(':forum_num',$cn);
        $pre->execute();
        $errorInfo = $pre->errorInfo();
		if($errorInfo[0] == 0){
			$uid = $pdo->lastInsertId();
			echo '{"msg":"已提交","no":0,"data":{"type":"up","uid":"'.$uid.'"}}';
		}else echo '{"msg":"写入数据失败","no":1}';
	}
}elseif($_POST["type"]=="up"){
    if(empty($_POST["cid"])) exit('{"error":"参数错误","no":1}');
	$cookie=$_POST["cookie"];
	$cid=$_POST["cid"];
	$name=$_POST["name"];
	if(empty($cookie)){
        $pre = $pdo->prepare("UPDATE tieba_sign_cookies SET uname=:name WHERE uid=:uid)");
        $pre->bindParam(':name',$name);
        $pre->bindParam(':uid',$_SESSION['uid']);
        $pre->execute();
		if($pre->rowCount()){
            exit('{"msg":"已修改","no":0,"data":{"type":"up","cid":"'.$cid.'"}}');
        }
	}
	$cn=testcookie($cookie);
    $pre = $pdo->prepare("UPDATE tieba_sign_cookies SET uname=:name,cookie=:cookie,status = 1,forum_num=:forum_num WHERE cid=:cid)");
    $pre->bindParam(':name',$name);
    $pre->bindParam(':cookie',$cookie);
    $pre->bindParam(':forum_num',$cn);
    $pre->bindParam(':cid',$cid);
    $pre->execute();
    if($pre->rowCount()){
        exit('{"msg":"已修改","no":0,"data":{"type":"up","uid":"'.$uid.'"}}');
    }
}elseif($_POST["type"]=="del"){
    if(empty($_POST["cid"])) exit('{"error":"参数错误","no":1}');
	$cid=$_POST["cid"];
    $pre = $pdo->prepare("UPDATE tieba_sign_cookies SET status=-1 WHERE status >=0 and uid=:uid and cid=:cid)");
    $pre->bindParam(':uid',$_SESSION['uid']);
    $pre->bindParam(':cid',$cid);
    $pre->execute();
	if($pre->rowCount())echo '{"msg":"已删除","no":0,"data":{"type":"up","cid":"'.$cid.'"}}';
	else echo '{"msg":"删除失败","no":1}';
}else{
	echo '{"error":"参数错误"}';
	exit();
}