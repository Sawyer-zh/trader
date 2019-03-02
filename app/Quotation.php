<?php

namespace App;

/**
 *
 * get stock data from sina api
 */
class Quotation
{
    
    private static $_urlFormat = 'http://hq.sinajs.cn/list=';

    /**
     * get recent price
     * @param $stockCode
     * @return array
     */
    public static function getRecentPrice($stockCode)
    {
/*{{{*/
        $prefix = self::getStockPrefix($stockCode);


        $ret =  Trader::curlRequest(self::$_urlFormat . $prefix.$stockCode, '', [], false);

        $ret =  iconv('gbk', 'utf8', $ret);
        
        preg_match('/\"(.*?)\"/', $ret, $matches);

        $value = explode(',', $matches[1]);

        $key = ['name' , 'today_open' , 'yes_close' ,'current', 'high' , 'low' , 'buy1' , 'sell1' , 'deal_num' , 'deal_amount','buy1_num','buy1_price', 'buy2_num','buy2_price', 'buy3_num','buy3_price', 'buy4_num','buy4_price', 'buy5_num','buy5_price','sell1_num','sell1_price', 'sell2_num','sell2_price', 'sell3_num','sell3_price', 'sell4_num','sell4_price', 'sell5_num','sell5_price','date','time', 'unknow'];

        $return = array_combine($key, $value);
        

        return $return;
    }/*}}}*/

    /**
     * 0 , 3 shenzhen stock
     * 6 shanghai stock
     * 5 shanghai fund
     * 12 shanghai convertable bond
     * 11 shenzhen convertable bond
     * other shenzhen
     * @param $stockCode
     * @return string 'sh' 'sz'
     */
    public static function getStockPrefix($stockCode)
    {
/*{{{*/
        $codeArr = str_split($stockCode);
        
        if (in_array($codeArr[0], [0,3])) {
            return 'sz';
        } elseif (in_array($codeArr[0], [5,6])) {
            return 'sh';
        } elseif ($codeArr[0] == 1) {
            if ($codeArr[1] == 1) {
                return 'sh';
            }
        
            return 'sz';
        } else {
            return 'sh';
        }
    }/*}}}*/
}
