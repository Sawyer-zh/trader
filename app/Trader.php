<?php

namespace App;

class Trader
{
    /**
     *
     * login
     */
    public static function login()
    {
/*{{{*/
        $uuid = self::generateUuid();
        $randNumber = self::generateRandNumber();
        
        self::getVerifyCode($uuid, $randNumber);
        
        $url = "https://tradeh5.eastmoney.com/LogIn/Authentication";
        
        $fp = fopen('/dev/stdin', 'r');
        
        echo "请输入验证码:\n";

        $input = fgets($fp, 255);
        
        fclose($fp);
        
        $input = chop($input);
       
        $config = json_decode(file_get_contents('config.json') ,true);
        $loginArr = [
            'userId'=> $config['user_id'],
            'password'=> $config['password'],
            'identifyCode'=> $input,
            'randNumber'=>Cache::get('randNumber'),
            'type'=>'Z',
            'holdOnlineIdx'=>'2'
        ];
        $ret = self::curlRequest($url, Cache::get('uuid'), $loginArr);
        

        return $ret;
    }/*}}}*/

    /**
     *
     * get Asset
     */
    public static function getAsset()
    {
        /*{{{*/
        $url = 'https://tradeh5.eastmoney.com/Assets/GetMyAssests';
        
        $ret =self::curlRequest($url, Cache::get('uuid'));
        
        return $ret;
    }/*}}}*/

    /**
     * trade api
     * @param $stockCode
     * @param $price
     * @param $amount
     * @param $tradeType
     * @return true
     */
    public static function trade($stockCode, $price, $amount, $tradeType)
    {
        /*{{{*/
        $url = 'https://tradeh5.eastmoney.com/Trade/SubmitTrade';

        $data = compact('stockCode', 'price', 'amount', 'tradeType');

        return self::curlRequest($url, Cache::get('uuid'), $data);
    }/*}}}*/

    /**
     * recall api
     * @param $delegateDate string '20180504'
     * @param $delegeteCode string weituobianhao
     */
    public static function recall($delegateDate, $delegeteCode)
    {
/*{{{*/
        $url = 'https://tradeh5.eastmoney.com/Trade/CancelOrder';
        return self::curlRequest($url, Cache::get('uuid'), compact('delegateDate', 'delegeteCode'));
    }/*}}}*/


    /**
     *
     * postion api
     */
    public static function getPosition()
    {
/*{{{*/
        $url = "https://tradeh5.eastmoney.com/Trade/GetMyStockList?pgSize=&dwc=";

        return self::curlRequest($url, Cache::get('uuid'));
    }/*}}}*/

    /**
     * today's entrust
     */
    public static function getEntrustToday()
    {
/*{{{*/
        $url = 'https://tradeh5.eastmoney.com/Search/GetOrdersData';
        $data = [
            'qqhs'=>20,
            'dwc'=>'',
        ];
        return self::curlRequest($url, Cache::get('uuid'), $data);
    }/*}}}*/

    /**
     *
     * get deal data
     */
    public static function getDealData()
    {
/*{{{*/
        $url = 'https://tradeh5.eastmoney.com/Search/GetDealData';
        $data=[
            'qqhs'=>20,
            'dwc'=>'',
        ];
        return self::curlRequest($url, Cache::get('uuid'), $data);
    }/*}}}*/


    public static function getVerifyCode($uuid, $randCode)
    {
/*{{{*/
        
        $url = "https://tradeh5.eastmoney.com/LogIn/YZM?randNum=$randCode";

        $pic = self::curlRequest($url, $uuid, '', false);

        $f = fopen('verify.png', 'w');
        
        fwrite($f, $pic);
        
        fclose($f);
    }/*}}}*/

    /**
     *
     * Uuid 32 string
     */
    public static function generateUuid()
    {
/*{{{*/
        $uuid =  md5(time());
        Cache::set('uuid', $uuid, 60 * 24);
        return $uuid;
    }/*}}}*/

    /**
     *
     * randcode 0.xxx 16 decimal
     */
    public static function generateRandNumber()
    {
/*{{{*/
        $str = '0.';
        for ($i=0; $i<16; $i++) {
            $str .= mt_rand(0, 9);
        }
        Cache::set('randNumber', $str, 60 * 24);
        return $str;
    }/*}}}*/

    /**
     * @param $url
     * @param $data array
     * @param $uuid string 32
     * @param $isPost boolean
     * @return mix
     */
    public static function curlRequest($url, $uuid, $data = [], $isPost = true)
    {
/*{{{*/

        $ch = curl_init();

//        $header = [
//            'Content-type: application/json;charset="utf-8"',
//            'User-Agent:https://tradeh5.eastmoney.com/Assets/Index',
//        ];
        
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIE, "Uuid=$uuid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome 42.0.2311.135 / Android 6.0');
       // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        $ret = curl_exec($ch);
        
        curl_close($ch);

        return $ret;
    }/*}}}*/
}
