<?php
include('function.php');
include('connect.php');
header("content-Type: text/html; charset=utf-8");

class tiebasign{
    private $db;
    private $uid;
    private $cid;
    private $bduss;
    private $tbs;
    private $mslevel;
    private $forumid;
    private $forum_name;
    
    const mylikeurl = 'http://c.tieba.baidu.com/c/f/forum/getforumlist';
    const signurl = 'http://c.tieba.baidu.com/c/c/forum/sign';
    const msignurl = 'http://c.tieba.baidu.com/c/c/forum/msign';
    const tbsurl = 'http://tieba.baidu.com/dc/common/tbs';
    
    public function __construct(){
        global $pdo;
        $this->db = $pdo;
    }
    
    private function _init_bduss(){
        $res = $this->db->query("SELECT uid,cid,cookie FROM tieba_sign_cookies WHERE end_sign < CURRENT_DATE() AND status=1 ORDER BY last_sign ASC LIMIT 1;")->fetch();
        if(empty($res)) return false;
        $this->uid = $res['uid'];
        $this->cid = $res['cid'];
        preg_match('/BDUSS=(.*?);/', $res['cookie'], $matches);
        if($matches[1]) $this->bduss = $matches[1]; else exit('bduss');
        
        $check = json_decode(curl_get(self::tbsurl,$this->bduss,false));
        if (!$check->is_login){
            $pre = $this->db->prepare("UPDATE tieba_sign_cookies SET status = 0 WHERE cid=:cid");
            $pre->bindParam(':cid',$this->cid);
            $pre->execute();
            $errorInfo = $pre->errorInfo();
            if($errorInfo[0] != 0) var_dump($errorInfo[2]);
            echo "当前用户可能是BDUSS/cookie设置错误了,切换到下一个用户。<br/>";
            $this->_init_();
        }
        $this->tbs = $check->tbs;
    }
    
    public function _init_(){
        $this->_init_bduss();
        $this->getmylike();
        $this->sign();
    }
    
    public function getmylike(){
        $day = date('Y-m-d');
        $pre = $this->db->prepare("UPDATE tieba_sign_cookies SET last_sign = CURRENT_TIMESTAMP() WHERE cid=:cid");
        $pre->bindParam(':cid',$this->cid);
        $pre->execute();
        $errorInfo = $pre->errorInfo();
        if($errorInfo[0] != 0) var_dump($errorInfo[2]);
        $pda = array(
            'BDUSS'=>$this->bduss
        );
        $result = curl_post($pda,self::mylikeurl);
        $jsonobj = json_decode($result,1);
        /* $pre = $this->db->prepare("replace into tieba_signlevel set level=:level, uid=:uid");
        $pre->bindParam(':level',$jsonobj['level']);
        $pre->bindParam(':uid',$this->uid);
        $pre->execute();
        $errorInfo = $pre->errorInfo();
        if($errorInfo[0] != 0) var_dump($errorInfo[2]); */
        $this->mslevel = $jsonobj['level'];
        
        $forumid = array();
        foreach ($jsonobj['forum_info'] as $key => $value) {
            if($value['is_sign_in'] == 1)continue;
            $forumid[$value['forum_id']] = $value['user_level'];
            $forum_name[$value['forum_id']] = $value['forum_name'];
            /* $pre = $this->db->prepare("INSERT INTO tieba_list (kw_name,forumid,level,uid) VALUES (:kw_name,:forumid,:level,:uid)");
            $pre->bindParam(':kw_name',$value['forum_name']);
            $pre->bindParam(':forumid',$value['forum_id']);
            $pre->bindParam(':level',$value['user_level']);
            $pre->bindParam(':uid',$this->uid);
            $pre->execute();
            $errorInfo = $pre->errorInfo();
            if($errorInfo[0] != 0) var_dump($errorInfo[2]); */
        }
        $this->forumid = $forumid;
        $this->forum_name = $forum_name;
        $i = count($jsonobj['forum_info']);
        $this->db->query("UPDATE tieba_sign_cookies SET forum_num = $i WHERE cid={$this->cid}");
        echo "获取结束,一共[ $i ]个贴吧。<br/>";
        return true;
    }
    
    
    public function sign(){
        $m_forumid = $s_forumid = array();
        foreach ($this->forumid as $forumid => $level) {
            if($level >= $this->mslevel) $m_forumid[] = $forumid;
            else $s_forumid[] = $forumid;
        }
        if($m_forumid){
            $pda = array(
                'BDUSS'=>$this->bduss,
                'tbs'=>$this->tbs,
                'forum_ids'=>implode(',',$m_forumid)
            );
            $result = curl_post($pda,self::msignurl);
            $jsonobj = json_decode($result,1);var_dump($jsonobj);
            if($jsonobj['error_code'] == 0 ){
                if($jsonobj['error']['errno'] == 0){
                    foreach ($jsonobj['info'] as $forum) {
                        if($forum['signed'] == 1){
                            $pre = $this->db->prepare("INSERT INTO tieba_sign_history (kw,cid,uid,type,time) VALUES (:kw_name,:cid,:uid,1,CURRENT_TIMESTAMP())");
                            $pre->bindParam(':kw_name',$forum['forum_name']);
                            $pre->bindParam(':cid',$this->cid);
                            $pre->bindParam(':uid',$this->uid);
                            $pre->execute();
                            $errorInfo = $pre->errorInfo();
                            if($errorInfo[0] != 0) var_dump($errorInfo[2]);
                            /* $pre = $this->db->prepare("DELETE FROM tieba_list WHERE kw_name = :kw_name AND uid=:uid");
                            $pre->bindParam(':kw_name',$forum['forum_name']);
                            $pre->bindParam(':uid',$this->uid);
                            $pre->execute();
                            $errorInfo = $pre->errorInfo();
                            if($errorInfo[0] != 0) var_dump($errorInfo[2]); */
                        }
                    }
                }else{
                    $pre = $this->db->prepare("INSERT INTO tieba_sign_error_code (code,usermsg,errmsg) VALUES (:code,:usermsg,:errmsg)");
                    $pre->bindParam(':code',$jsonobj['error']['errno']);
                    $pre->bindParam(':usermsg',$jsonobj['error']['usermsg']);
                    $pre->bindParam(':errmsg',$jsonobj['error']['errmsg']);
                    $pre->execute();
                    $errorInfo = $pre->errorInfo();
                    if($errorInfo[0] != 0) var_dump($errorInfo[2]);
                }
                
            }
        }
        if($s_forumid){
            $pda = array(
                'BDUSS'=>$this->bduss,
                'tbs'=>$this->tbs,
            );
            foreach ($s_forumid as $forumid) {
                $pda['fid'] = $forumid;
                $pda['kw'] = $this->forum_name[$forumid];
                $result = curl_post($pda,self::signurl);
                $jsonobj = json_decode($result,1);var_dump($jsonobj);
                if($jsonobj['error_code'] == 0 ){
                    if($jsonobj['user_info']['is_sign_in'] == 1){
                        $pre = $this->db->prepare("INSERT INTO tieba_sign_history (kw,cid,uid,type,time) VALUES (:kw_name,:cid,:uid,1,CURRENT_TIMESTAMP())");
                        $pre->bindParam(':kw_name',$forum['forum_name']);
                        $pre->bindParam(':cid',$this->cid);
                        $pre->bindParam(':uid',$this->uid);
                        $pre->execute();
                        $errorInfo = $pre->errorInfo();
                        if($errorInfo[0] != 0) var_dump($errorInfo[2]);
                        /* $pre = $this->db->prepare("DELETE FROM tieba_list WHERE kw_name = :kw_name AND uid=:uid");
                        $pre->bindParam(':kw_name',$this->forum_name[$forumid]);
                        $pre->bindParam(':uid',$this->uid);
                        $pre->execute();
                        $errorInfo = $pre->errorInfo();
                        if($errorInfo[0] != 0) var_dump($errorInfo[2]); */
                    }
                    
                }else{
                    $pre = $this->db->prepare("INSERT INTO tieba_sign_error_code (code,usermsg,errmsg) VALUES (:code,:usermsg,:errmsg)");
                    $pre->bindParam(':code',$jsonobj['error_code']);
                    $pre->bindParam(':usermsg',$forumid);
                    $pre->bindParam(':errmsg',$jsonobj['error_msg']);
                    $pre->execute();
                    $errorInfo = $pre->errorInfo();
                    if($errorInfo[0] != 0) var_dump($errorInfo[2]);
                }
            }
        }
        
        $this->db->query("UPDATE tieba_sign_cookies SET last_sign = CURRENT_TIMESTAMP() WHERE cid={$this->cid}");
        //签到完成检查是否都签到成功
        $this->getmylike();
        if(empty($this->forumid)){
            $this->db->query("UPDATE tieba_sign_cookies SET end_sign = CURRENT_DATE() WHERE cid={$this->cid}");
        }
        
    }
}

$obj = new tiebasign();
$obj->_init_();