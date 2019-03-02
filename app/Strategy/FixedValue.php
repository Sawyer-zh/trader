<?php

namespace App\Strategy;

use App\Strategy;
use App\User;
use App\Stock;

/**
 * an example:
 * initial:
 *   fixed market value : 10000
 *   now price: 1
 * when price 1.1:
 *   now market value : 11000
 *   sell 1000 / 1.1 amount
 * when price 0.9:
 *   now market value : 9000
 *   buy 1000 / 0.9 amount
 */
class FixedValue extends Strategy
{
    // total value
    protected $fixedValue;

    // percentage trigger
    protected $rate = 2;

    // config
    protected $config = [
        '512000'=>[
            'fixedValue'=>36000,
            'rate'=>1,
        ],
    ];



    /**
     * excute concrete method
     */
    public function executeStrategy()
    {
        $value = array_values($this->user->getPositionByCode($this->stock->code))[0]['Zxsz'];
        // assign current stock config
        $this->fixedValue = $this->config[$this->stock->code]['fixedValue'];
        $this->rate = $this->config[$this->stock->code]['rate'];
    
        $change = $this->fixedValue * $this->rate / 100;
        if ($value <= $this->fixedValue + $change && $value >= $this->fixedValue - $change) {
            return false;
        }
        
        if ($value > $this->fixedValue) {
            $amount = floor(( $value - $this->fixedValue ) / $this->stock->buy1 / 100) * 100;
            $this->sell($amount);
        }
        
        if ($value < $this->fixedValue) {
            $amount = floor(( $this->fixedValue  - $value) / $this->stock->sell1 / 100) * 100;
            $this->buy($amount);
        }

        // update position
        $this->user->getPosition();
        return true;
    }
}
