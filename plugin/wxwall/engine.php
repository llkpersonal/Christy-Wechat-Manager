<?php

require_once(dirname(__FILE__).'/../plugin.inc.php');

class Engine{
    private $cookiefilepath; //= dirname(__FILE__).'/cookie.txt';
    private $webtokenStoragefile;// = dirname(__FILE__).'/token.txt';
    private $protocol;
    private $email;
    private $password;
    private $webtoken;
    
    public function __construct(){
        $this->cookiefilepath = dirname(__FILE__).'/cache/cookiefile/'.$_GET['wid'].'.txt';
        $this->webtokenStoragefile = dirname(__FILE__).'/cache/tokenfile/'.$_GET['wid'].'.txt';
        $this->protocol = "https";
        if(file_exists($this->webtokenStoragefile))
            $this->webtoken = file_get_contents($this->webtokenStoragefile);
        $aDatabase = new Database();
        $result = $aDatabase->get_result("SELECT * FROM `weconfig` WHERE `wid`=".$_GET['wid']);
        $arr = mysql_fetch_array($result);
        $this->email = $arr['email'];
        $this->password = $arr['password'];
        unset($aDatabase);
    }
    
    public function login(){
        $url = $this->protocol."://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN";
        $postfields["username"] = $this->email;
        $postfields["pwd"] = $this->password;
        $postfields["f"] = "json";
        $postfieldss = "username=".urlencode($this->email)."&pwd=".urlencode($this->password)."&f=json";
        $response = $this->post($url, $postfields, $this->protocol."://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN");
        $result = json_decode($response, true);
        if ($result['ErrCode']=="65201"||$result['ErrCode']=="65202"||$result['ErrCode']=="0")
        {
            preg_match('/&token=([\d]+)/i', $result['ErrMsg'],$match);
            file_put_contents($this->webtokenStoragefile, $match[1]);
            $this->webtoken = $match[1];
            return true;
        }
        else
        {
            unlink($this->cookiefilepath);
            return false;
        }
    }
    
    public function send($fakeid,$content)
    {
        //判断cookie是否为空，为空的话自动执行登录
        if (file_exists($this->cookiefilepath)||true===$this->login())
        {
            $postfields = array();
            $postfields['tofakeid'] = $fakeid;
            $postfields['type'] = 1;
            $postfields['error']= "false";
            $postfields['token']= $this->webtoken;
            $postfields['content'] = $content;
            $postfields['ajax'] = 1;
            $url = $this->protocol."://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response";
            $response = $this->post($url, $postfields, $this->protocol."://mp.weixin.qq.com/");
            $tmp = json_decode($response,true);
            //判断发送结果的逻辑部分
            if ('ok'==$tmp["msg"]) {
                return 1;
            }
            elseif ($tmp['ret']=="-2000")
            {
                return -1;
            }
            else
            {
                return false;
            }
        }
        else  //登录失败返回false
        {
            return false;
        }
    }
    
    public function checkValid()
    {
        $postfields = array();
        $url = $this->protocol."://mp.weixin.qq.com/cgi-bin/getregions?id=1054&t=ajax-getregions&lang=zh_CN&token=".$this->webtoken;
        //判断cookie是否为空，为空的话自动执行登录
        if (file_exists($this->cookiefilepath))
        {
            $response = $this->get($url, $this->protocol."://mp.weixin.qq.com/cgi-bin/userinfopage?t=wxm-setting&token=383506232&lang=zh_CN");
            $result = json_decode($response,1);
            if(isset($result['num'])){
                return true;
            }else{
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function get_information($wid,$openid,$varify,$time){
        if(false===$this->checkValid())$this->login();
        $token = $this->webtoken;
        $page_content = $this->get("https://mp.weixin.qq.com/cgi-bin/message?t=message/list&count=100&day=7&token=$token&lang=zh_CN","https://mp.weixin.qq.com/cgi-bin/message?t=message/list&count=100&day=7&token=$token&lang=zh_CN");
        if(preg_match_all("/\{\"id\":.*?\"refuse_reason\":\"\"\}/",$page_content,$array)){
            foreach($array[0] as $val){
                $obj = json_decode($val);
                if($obj->content==$varify && $obj->date_time==$time){
                    $aDatabase = new Database();
                    $aDatabase->get_result("UPDATE `wxwall_user` SET `username`='$obj->nick_name',`fakeid`='$obj->fakeid' WHERE `openid`='$openid' AND `wid`='$wid';");
                    unset($aDatabase);
                    return true;
                }
            }
        }
        return false;
    }
    
    public function get_head_img($wid,$openid){
        $aDatabase = new Database();
        $row = mysql_fetch_array($aDatabase->get_result("SELECT * FROM `wxwall_user` WHERE `wid`='$wid' AND `openid`='$openid';"));
        $uid = $row['uid'];
        $fakeid = $row['fakeid'];
        unset($aDatabase);
        if(false===$this->checkValid())$this->login();
        $token = $this->webtoken;
        $jpgcontent = $this->get("https://mp.weixin.qq.com/cgi-bin/getheadimg?fakeid=$fakeid&token=$token&lang=zh_CN","https://mp.weixin.qq.com/cgi-bin/getheadimg?fakeid=$fakeid&token=$token&lang=zh_CN");
        file_put_contents(dirname(__FILE__)."/headimg/$uid.jpg",$jpgcontent);
        return $fakeid;
    }
    
    private function post($url, $postfields, $referer)
    {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,         // return web page
            CURLOPT_HEADER         => false,        // don't return headers
            CURLOPT_FOLLOWLOCATION => false,         // follow redirects
            CURLOPT_ENCODING       => "",           // handle all encodings
            CURLOPT_USERAGENT      => "",     // who am i
            CURLOPT_AUTOREFERER    => true,         // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
            CURLOPT_TIMEOUT        => 120,          // timeout on response
            CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
            CURLOPT_POST            => true,            // i am sending post data
            CURLOPT_POSTFIELDS     => $postfields,    // this are my post vars
            CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false,        //
            CURLOPT_COOKIEFILE     =>$this->cookiefilepath,
            CURLOPT_COOKIEJAR      =>$this->cookiefilepath,
            CURLOPT_REFERER        =>$referer,
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    private function get($url, $referer)
    {
        $info = null;
        $ch = curl_init($url);
        $options = array(
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_HEADER         => false,        // don't return headers
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
                CURLOPT_ENCODING       => "",           // handle all encodings
                CURLOPT_USERAGENT      => "",     // who am i
                CURLOPT_AUTOREFERER    => true,         // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
                CURLOPT_TIMEOUT        => 120,          // timeout on response
                CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_COOKIEFILE     =>$this->cookiefilepath,
                CURLOPT_COOKIEJAR      =>$this->cookiefilepath,
                CURLOPT_REFERER        =>$referer,
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}


/*$aEngine = new Engine();
if($aEngine->get_information($_GET['wid'],'okzWBjpsl2eUWMrN3PZKwlCUaH7o','okzwbj','1391577677')) echo 'ok';
else echo 'failed';*/

?>