<?php
/**
 * Created by PhpStorm.
 * User: MainAccount
 * Date: 6/6/2020
 * Time: 2:40 PM
 */

namespace App\Exchangers;


use App\Models\MoneyStore;

class Exchangers
{

    public static function AddchangeToMainExchanage($data = array())
    {


        if (isset($data) && count($data) > 0) {


            $money_store_before = \DB::table('money_store')->where('store_id', $money_store);

            if ($money_store_before->value('currency_id') == $currency_id) {


                $money_store_before->update([
                    'money_amount' => $money_store_before->value('money_amount') + $mount
                ]);


            } else {


                //   20000 $          x AFG
                // ----------   =   ----------
                //

                $bproducts = \DB::table('currency_exchange');


                if ($bproducts->where("main_currency_id", $money_store_before->value('currency_id'))
                        ->where('other_currency_id', $currency_id)->count() > 0) {

                    $bproducts = $bproducts->where("main_currency_id", $money_store_before->value('currency_id'))
                        ->where('other_currency_id', $currency_id);

                } else {

                    $bproducts = $bproducts->where("main_currency_id", $money_store_before->value('currency_id'))
                        ->where('other_currency_id', $currency_id);


                }
//            $bproducts_result = $bproducts->value('money_amount') < $bproducts->value('exchange_rate') ?
//                $bproducts->value('exchange_rate') / $bproducts->value('money_amount') :
//                $bproducts->value('money_amount') * $bproducts->value('exchange_rate');

//            dd(($bproducts * $mount));
                $money_store_before->update([
                    'money_amount' => $money_store_before->value('money_amount')
                ]);

            }
        }


    }


}