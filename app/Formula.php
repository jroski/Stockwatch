<?php

namespace App;

use Illuminate\Support\Facades\DB;


class Formula
{
    public $lookup_table = [];

    public function __construct()
    {
        $this->lookup_table = DB::table('formula_reference')
                                ->select('from', 'penalty', 'bonus', 'multiplier', 'to')
                                ->get()
                                ->mapToAssoc(static function ($value, $key) {
                                    return [$value->from . $value->to, $value];
                                })->toArray();
    }

    public function calculate($from, $to, $previousPrice, $strikes)
    {
        /** =round(((D3*G3)*(H3*(0.9^(D3*G3))+1))*(1-(K3*(1-(0.97^(L3))))),2)
         *  =round(((D3*G3)*(H3*(0.9^(D3*G3))+1))*(1-(K3*(1-(0.97^(L3))))),2)
         *
         * D3 - Last Weeks Price
         * G3 - Multiplier
         * H3 - Bonus
         * K3 - Penalty
         * L3 - Strikes (weeks <= 4)
         */

        $ref = $this->lookup_table[$from . $to];

        $multiplier = $ref->multiplier;
        $penalty = $ref->penalty;
        $bonus = $ref->bonus;
//        dump('ref:');
//        dump($ref);
//        dump('previousPrice: '.$previousPrice);
//        dump('strikes: '.$strikes);
//
//        dump('($previousPrice * $multiplier): '.($previousPrice * $multiplier));
//        dump('pow(0.9, $previousPrice * $multiplier): '.pow(0.9, $previousPrice * $multiplier));
//        dump('($bonus * pow(0.9, $previousPrice * $multiplier) + 1)): '.($bonus * pow(0.9, $previousPrice * $multiplier) + 1));
//
//        dump('(($previousPrice * $multiplier) * ($bonus * (0.9 ^ ($previousPrice * $multiplier)) + 1)) * (1 - ($penalty * (1 - (0.97 ^ $strikes))))');
//        dump("(({$previousPrice} * {$multiplier}) * ({$bonus} * (0.9 ^ ({$previousPrice} * {$multiplier})) + 1)) * (1 - ({$penalty} * (1 - (0.97 ^ {$strikes}))))");
//        dump('('.$previousPrice * $multiplier.' * ('.$bonus.' * (0.9 ^ '.$previousPrice * $multiplier.') + 1)) * (1 - ('.$penalty.' * (1 - (0.97 ^ '.$strikes.'))))');
//        dump('('.$previousPrice * $multiplier.' * ('.$bonus.' * '.pow(0.9, $previousPrice * $multiplier).' + 1)) * (1 - ('.$penalty.' * (1 - '.pow(0.97, $strikes).')))');
//        dump('('.$previousPrice * $multiplier.' * ('.$bonus * pow(0.9, $previousPrice * $multiplier).' + 1)) * (1 - ('.$penalty.' * '. (int) (1 - pow(0.97, $strikes)).'))');
//        dump('('.$previousPrice * $multiplier.' * '. (($bonus * pow(0.9, $previousPrice * $multiplier)) + 1) .') * (1 - '. ($penalty * (1 - pow(0.97, $strikes))).')');
//        dump(($previousPrice * $multiplier * (($bonus * pow(0.9, $previousPrice * $multiplier)) + 1) .' * ' . (1 - ($penalty * (1 - pow(0.97, $strikes))))));
//        dump((string)($previousPrice * $multiplier * (($bonus * pow(0.9, $previousPrice * $multiplier)) + 1) * (1 - ($penalty * (1 - pow(0.97, $strikes))))));
//        dump((string)number_format((($previousPrice * $multiplier) * ($bonus * pow(0.9, $previousPrice * $multiplier) + 1)) * (1 - ($penalty * (1 - pow(0.97, $strikes)))),2));



        return number_format((($previousPrice * $multiplier) * ($bonus * pow(0.9, $previousPrice * $multiplier) + 1)) * (1 - ($penalty * (1 - pow(0.97, $strikes)))), 2);
    }
}
