<?php

namespace App;

abstract class Strategy
{

    //object stock
    protected $stock;

    //object user
    protected $user;

    public function __construct()
    {
/*{{{*/
        $this->stock = new Stock;
        $this->user = new User;
    }/*}}}*/


    public function execute()
    {
        /*{{{*/
        while (1) {
            foreach ($this->config as $key => $value) {
                $this->stock->code = $key;
                // refresh recent price
                $this->stock->recentPrice();
                // refresh current position
                $this->user->getPosition();

                $ret =  $this->executeStrategy();

                if ($ret) {
                    $this->confirmStrategy();
                }
                sleep(2);
            }
            sleep(30);
        }
    }/*}}}*/



    abstract public function executeStrategy();

    /**
     * @param $amount
     *
     * buy
     */
    public function buy($amount)
    {
/*{{{*/
        $this->stock->type = 'B';
        $this->stock->amount = $amount;
        return $this->stock->trade();
    }/*}}}*/

    /**
     * @param $amount
     *
     * sell
     */
    public function sell($amount)
    {
/*{{{*/
        $this->stock->type = 'S';
        $this->stock->amount = $amount;
        return $this->stock->trade();
    }/*}}}*/

    /**
     *
     * comfirm the trade
     */
    public function confirmStrategy()
    {
/*{{{*/
        $i = 0;
        while ($i<3) {
            sleep(30);
            $this->stock->getDealData();
            $ret = array_filter($this->stock->deal, function ($item) {
                return $item['Wtbh'] == $this->stock->entrust;
            });


            if (!empty($ret)) {
                return ;
            }
            $i++;
        }

        $this->stock->recall($this->stock->entrust);
        
        sleep(30);
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
}
