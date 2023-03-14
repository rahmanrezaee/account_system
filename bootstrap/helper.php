<?php

function get_options($option_name)
{
    return DB::table("options")->where("option_name", $option_name);
}

function get_meladi($date)
{


//    dd($date);
    return \Morilog\Jalali\CalendarUtils::createDatetimeFromFormat('Y-m-d', $date)->format('Y/m/d');

}

function get_customer_name($id)
{
    return DB::table("customer")->where("customer_id", $id);
}

function get_unit($id)
{
    return DB::table("product_unit")->where("unit_id", $id);
}

function change_number_to_alphba($number)
{

    $result = '';


    if ($number < 100) {

        return numberTableQuery($number);


    }
    if ($number < 1000) {

        return numberLeastThounsnd($number);

    }


    if ($number < 1000000) {

        return numberLeastHandThounsnd($number);

    }
    if ($number < 1000000000) {

        return numberthounsendMelleon($number);

    }


}


function numberTableQuery($number)
{

    return DB::table("number_table")->where("nt_id", $number)->value('nt_name');

}

function numberLeastThounsnd($number)
{

    //1

    $mainNumber = $number / 100;
    $reminder = $number % 100;

    if ($reminder != 0) {
        $reminder = ' و ' . numberTableQuery($reminder);
    } else {
        $reminder = "";
    }
    return numberTableQuery(floor($mainNumber)) . ' ' . numberTableQuery(100) . $reminder;

}

function numberLeastHandThounsnd($number)
{


    // 1001

    //1

    //1

    $mainNumber = $number / 1000;
    $reminder = $number % 1000;


    if ($reminder != 0) {

        if (floor(($reminder)) > 100) {

            $reminder = ' و ' . numberLeastThounsnd(floor(($reminder)));

        } else {
            $reminder = ' و ' . numberTableQuery(floor(($reminder)));
        }

//        $reminder =' و '. numberLeastThounsnd($reminder);

    } else {

        $reminder = "";

    }


    if (floor(($mainNumber)) > 100) {

        $mainNumber = numberLeastThounsnd(floor(($mainNumber)));

    } else {
        $mainNumber = numberTableQuery(floor(($mainNumber)));
    }

    return $mainNumber . ' ' . numberTableQuery(1000) . ' ' . $reminder;

}

function numberthounsendMelleon($number)
{

    $mainNumber = $number / 1000000;
    $reminder = $number % 1000000;

    if ($reminder != 0) {

        if (floor(($reminder)) > 1000) {

            $reminder = ' و ' . numberLeastHandThounsnd(floor(($reminder)));

        } else if (floor(($reminder)) > 100) {

            $reminder = ' و ' . numberLeastThounsnd(floor(($reminder)));

        } else {
            $reminder = ' و ' . numberTableQuery(floor(($reminder)));
        }


    } else {
        $reminder = "";
    }

    if (floor(($mainNumber)) > 1000) {

        $mainNumber = numberLeastHandThounsnd(floor(($mainNumber)));

    } else if (floor(($mainNumber)) > 100) {

        $mainNumber = numberLeastThounsnd(floor(($mainNumber)));

    } else {
        $mainNumber = numberTableQuery(floor(($mainNumber)));
    }

    return $mainNumber . ' ' . numberTableQuery(1000000) . ' ' . $reminder;

}


function getCurrency($id)
{

    return DB::table("currency")->where("currency_id", $id)->value('currency_name');

}

function getAccountName($id)
{

    return DB::table('money_store')->where('store_id', $id)->value('name');

}

function getProductName($id)
{

    return DB::table("product")->where("product_id", $id)->value("product_name");
}

function getCurrencyName($id)
{

    return DB::table('currency')->where('currency_id', $id)->value('currency_name');

}

function getUnitsExchangeList($id){




    return DB::table("unit_exchange")
        ->join("product_unit", "unit_id", "=", "relate_unit_id")
        ->where("main_unit_id",$id)->get();

}