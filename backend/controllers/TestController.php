<?php
/**
 * Created by PhpStorm.
 * User: Sam
 * Date: 2016/11/15
 * Time: 15:53
 */

namespace backend\controllers;


use yii\web\Controller;

class TestController extends Controller
{
    public function actionTest()
    {
        $t = file_get_contents("http://m.ip138.com/31/ningbo/yinzhou/tianqi/");
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
        echo $con;


    }
}