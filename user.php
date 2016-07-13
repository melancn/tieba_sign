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
	$result = $pdo->query('SELECT uid,cname,ct,tnum FROM sign_notes');
    while($r = $result->fetch()){
		$a[] = $r;
	}
	echo json_encode($a);
}elseif($_POST["type"]=="new"){
	$cookie=$_POST["cookie"];
	$name=$_POST["name"];
    $pre = $this->db->prepare("SELECT * FROM sign_notes WHERE cname=:name");
    $pre->bindParam(':name',$name);
    $pre->execute();
	if($pre->fetch()){
		echo '{"msg":"代号重复","no":1}';
		exit();
	}
	if($cn=testcookie($cookie)){
        $pre = $this->db->prepare("INSERT INTO sign_notes (cname,cookie,tnum) VALUES (:name,:cookie,:tnum)");
        $pre->bindParam(':name',$name);
        $pre->bindParam(':cookie',$cookie);
        $pre->bindParam(':tnum',$cn);
        $pre->execute();
        $errorInfo = $pre->errorInfo();
		if($errorInfo[0] == 0){
			$uid = $pre->lastInsertId();
			echo '{"msg":"已提交","no":0,"data":{"type":"up","uid":"'.$uid.'","cn":"'.$cn.'"}}';
		}else echo '{"msg":"写入数据失败","no":1}';
	}
}elseif($_POST["type"]=="up"){
	$cookie=$_POST["cookie"];
	$uid=$_POST["uid"];
	$name=$_POST["name"];
	$cn=$_POST["cn"];
	if(empty($cookie)){
        $pre = $this->db->prepare("UPDATE sign_notes SET cname=:name WHERE uid=:uid)");
        $pre->bindParam(':name',$name);
        $pre->bindParam(':uid',$uid);
        $pre->execute();
		if($pre->rowCount()){
            exit('{"msg":"已修改","no":0,"data":{"type":"up","uid":"'.$uid.'","cn":"'.$cn.'"}}');
        }
	}
	$cn=testcookie($cookie);
    $pre = $this->db->prepare("UPDATE sign_notes SET cname=:name,cookie=:cookie,ct = 1,tnum=:tnum WHERE uid=:uid)");
    $pre->bindParam(':name',$name);
    $pre->bindParam(':cookie',$cookie);
    $pre->bindParam(':tnum',$cn);
    $pre->bindParam(':uid',$uid);
    $pre->execute();
    if($pre->rowCount()){
        exit('{"msg":"已修改","no":0,"data":{"type":"up","uid":"'.$uid.'","cn":"'.$cn.'"}}');
    }
}elseif($_POST["type"]=="del"){
	$uid=$_POST["uid"];
    $pre = $this->db->prepare("DELETE FROM sign_notes WHERE uid=:uid)");
    $pre->bindParam(':uid',$uid);
    $pre->execute();
	if($pre->rowCount())echo '{"msg":"已删除","no":0,"data":{"type":"up","uid":"'.$uid.'"}}';
	else echo '{"msg":"删除失败","no":1}';
}else{
	echo '{"error":"参数错误"}';
	exit();
}