<?php

namespace App;

use Exception;

class User
{
    
    // array position
    protected $position;

    // array total asset
    // key: RMBZzc , Zzc , Zxsz , Kyzj , Kqzj , Djzj, Zjye ...
    protected $asset;


    /**
     *
     * get total asset
     */
    public function __construct()
    {
    }

    /**
     * login
     */
    public function login()
    {
/*{{{*/
        return Trader::login();
    }/*}}}*/

    /**
     * asset
     */
    public function getAsset()
    {
/*{{{*/
        $ret = json_decode(Trader::getAsset(), true);
        if (empty($ret) || $ret['Status'] != 0) {
            throw new Exception("obtain asset error!:{$ret['Message']}");
        }
        $this->asset = $ret['Data'];

        return $this->asset;
    }/*}}}*/


    /**
     *
     * position
     */
    public function getPosition()
    {
/*{{{*/
        $ret = json_decode(Trader::getPosition(), true);
        if (empty($ret) || $ret['Status'] != 0) {
            throw new Exception("error get position:{$ret['Message']}");
        }
        $this->position = $ret['Data'];


        return $this->position;
    }/*}}}*/

    /**
     * @param $code
     */
    public function getPositionByCode($code)
    {
/*{{{*/
        return array_filter($this->position, function ($item) use ($code) {
            return $item['Zqdm'] == $code;
        });
    }/*}}}*/
}
