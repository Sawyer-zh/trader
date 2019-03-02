<?php

namespace App;

use Exception;

class Stock
{

    // recent price
    protected $price;

    // buy1 price
    protected $buy1;

    // sell1 price
    protected $sell1;

    // string type
    protected $type;

    // order amount
    protected $amount;

    // string code
    protected $code;

    // recent entrust weituobianhao
    protected $entrust;

    // today's entrust
    protected $entrustToday;

    // today's deal
    protected $deal;

    // code strategy
    public function __construct($code = null)
    {
/*{{{*/
        $this->code = $code;
    }/*}}}*/


    public function __set($field, $value)
    {
/*{{{*/
        $this->$field = $value;
    }/*}}}*/

    public function __get($field)
    {
/*{{{*/
        return $this->$field;
    }/*}}}*/

    /**
     * get recent price
     */
    public function recentPrice()
    {
/*{{{*/
        
        $ret =  Quotation::getRecentPrice($this->code);

        $this->current = $ret['current'];

        $this->buy1 = $ret['buy1'];

        $this->sell1 = $ret['sell1'];

        return $this->current;
    }/*}}}*/

    /**
     * trade
     */
    public function trade()
    {
/*{{{*/

        switch ($this->type) {
            case 'B':
                $price = $this->sell1;
                break;
            case 'S':
                $price = $this->buy1;
                break;
            default:
                throw new Exception('please assign deal type!');
                break;
        }

        $ret = json_decode(Trader::trade($this->code, $price, $this->amount, $this->type), true);
        
        if (empty($ret) || $ret['Status'] != 0) {
            throw new Exception("entrust error : {$ret['Message']}");
        }

        echo $this->type ,',' , $this->code ,  ',',$price ,',', $this->amount ,  ',' , date('H:i:s') , "\n";

        $entrust = $ret['Data'][0]['Wtbh'];

        $this->entrust = $entrust;

        return $entrust;
    }/*}}}*/

    /**
     *
     * recall
     */
    public function recall()
    {
/*{{{*/
        $delegeteCode = $this->entrust;
        $delegateDate = date('Ymd');
        $ret = Trader::recall($delegateDate, $delegeteCode);
        echo 'recall' , $delegeteCode , $delegateDate, date('H:i:s') ,"\n" ;
        return json_decode($ret)->Status == 0;
    }/*}}}*/

    /**
     *
     * get today's entrust
     */
    public function getEntrustToday()
    {
/*{{{*/
        $ret = json_decode(Trader::getEntrustToday(), true);
        if (empty($ret) || $ret['Status'] != 0) {
            throw new Exception("entrust error :{$ret['Data']}");
        }
        $this->entrustToday = $ret['Data'];
        return $this->entrustToday;
    }/*}}}*/
    

    /**
     * get deal data
     */
    public function getDealData()
    {
/*{{{*/
        $ret = json_decode(Trader::getDealData(), true);
        if (empty($ret) || $ret['Status'] != 0) {
            throw new Exception("deal error :{$ret['Data']}");
        }
        $this->deal= $ret['Data'];
        return $this->deal;
    }/*}}}*/
}
