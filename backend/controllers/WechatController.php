<?php
/**
 * Created by PhpStorm.
 * User: Sam
 * Date: 2016/11/15
 * Time: 16:00
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;

class WechatController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionKuaidi()
    {
	echo 'test';exit;
    }

    public function actionTest() 
    {
	$keyword = "快递70759230519534";
	$kd_num = substr($keyword, 6);
        $url = "http://biz.trace.ickd.cn/auto/" . $kd_num . "?mailNo=" . $kd_num . "&spellName=&exp-textName=&ts=123456&enMailNo=123456789&callback=_jqjsp&_1480785159090=";
        $str = file_get_contents($url); 
        $tmp_str = strstr($str, '{');
        $newstr = substr($tmp_str,0,strlen($tmp_str)-1);
        $tmp_cont = json_decode($newstr);
        $tst = $tmp_cont->data;
        $str_final = "";
        foreach ($tst as $k => $v) {
            $str_final .= $v->time . $v->context ."\n";

        }
	var_dump($str_final);
    }

    public function actionIndex()
    {
//        //获得参数 signature nonce token timestamp echostr
//        $nonce = $_GET['nonce'];
//        $token = 'weixin';
//        $timestamp = $_GET['timestamp'];
//        $echostr = $_GET['echostr'];
//        $signature = $_GET['signature'];
//        //形成数组，然后按字典序排序
//        $array = array();
//        $array = array($nonce, $timestamp, $token);
//        sort($array);
//        //拼接成字符串,sha1加密 ，然后与signature进行校验
//        $str = sha1(implode($array));
//        if ($str == $signature && $echostr) {
//            //第一次接入weixin api接口的时候
//            echo $echostr;
//            exit;
//        } else {
        $this->responseMsg();
//        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = file_get_contents('php://input');
        //extract post data
        if (!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $msgType = $postObj->MsgType;//消息类型
            $event = $postObj->Event;//时间类型，subscribe（订阅）、unsubscribe（取消订阅）
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            switch($msgType){
                case "event":
                    if($event=="subscribe"){
                        $contentStr = "Sam大帅比！！ 谢谢关注  but开发测试专用 有兴趣的东西可以留言喔";
                    }
                    break;
                case "text":
                    switch($keyword){
                        case "黎洋":
                            $contentStr = "大熊弟你是有多喜欢在下。。还输在下名字啊？ 哈哈哈";
                            break;
                        case substr($keyword, 0, 6) == "快递":
                            $kd_num = substr($keyword, 6);
                            $url = "http://biz.trace.ickd.cn/auto/" . $kd_num . "?mailNo=" . $kd_num . "&spellName=&exp-textName=&ts=123456&enMailNo=123456789&callback=_jqjsp&_1480785159090=";
                            $str = file_get_contents($url);
                            $tmp_str = strstr($str, '{');
                            $newstr = substr($tmp_str,0,strlen($tmp_str)-1);
                            $tmp_cont = json_decode($newstr);
                            $tst = $tmp_cont->data;
                            $str_final = "";
                            foreach ($tst as $k => $v) {
                                $str_final .= $v->time . $v->context ."\n\n";

                            }
                            $contentStr = $str_final;
			                break;
                        case (strpos($keyword, '天气') != false) :
                            if (substr($keyword, 0, 6) == "宁波") {
                                $t = file_get_contents("http://m.ip138.com/31/ningbo/yinzhou/tianqi/");
                            } elseif (substr($keyword, 0, 6) == "嘉兴") {
                                $t = file_get_contents("http://m.ip138.com/31/jiaxing/haiyan/tianqi/");
                            } else {
                                $contentStr = "目前只支持宁波天气和嘉兴天气";
                                break;
                            }
                            $rule = '/<ul class="query-hd">.+<\/ul>/';
                            preg_match($rule, $t, $result);
                            $re = '/<li>.+<\/li>/';
                            preg_match($re, $result[0], $match);

                            $regex0 = "/<div class=\"date\">.*?<\/div>/";
                            preg_match_all($regex0, $match[0], $date);
                            $re_date = preg_replace("/<div class=\"date\">/", '', $date[0]);
                            $re_date = preg_replace("/<\/div>/", '', $re_date);
                            $re_date = preg_replace("/<font color=\"red\">/", '', $re_date);
                            $re_date = preg_replace("/<\/font>/", '', $re_date);

                            $regex1 = "/<div class=\"phrase\">.*?<\/div>/";
                            preg_match_all($regex1, $match[0], $weather);
                            $delete_re = "/<img.*?>/";
                            $re_weacher = preg_replace($delete_re, '', $weather[0]);
                            $re_weacher = preg_replace("/<div class=\"phrase\">/", '', $re_weacher);
                            $re_weacher = preg_replace("/<\/div>/", '', $re_weacher);


                            $regex2 = "/<div class=\"temperature\">.*?<\/div>/";
                            preg_match_all($regex2, $match[0], $temperature);
                            $re_temperature = preg_replace("/<div class=\"temperature\">/", '', $temperature[0]);
                            $re_temperature = preg_replace("/<\/div>/", '', $re_temperature);

                            $con = "";
                            foreach ($re_date as $k => $v) {
                                $con .= $v.$re_weacher[$k].$re_temperature[$k]. "\n\n";
                            }
                            $contentStr = $con;
                            break;
                        case "test":
                            $contentStr = "没啥 自己测试用用。";
                            break;
			case "狐臭王":
                            $contentStr = "徐雨晨是狐臭王！！狐臭！！狐王！！";
                            break;
                        default:
                            $contentStr = "暂时没有对应的回复 喜欢什么回复  留言告诉我～";
                    }
                    break;
            }
            $msgType = "text";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        } else {
            echo "";
            exit;
        }
    }


}
