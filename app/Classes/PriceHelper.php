<?php

namespace App\Classes;

class PriceHelper
{
    /*
     * Todo: Coding Test for Technical Hires
     * Please read the instructions on the README.md
     * Your task is to write the functions for the PriceHelper class
     * A set of sample test cases and expected results can be found in PriceHelperTest
     */

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the unit price of an item based on the quantity.
     *
     * Question:
     * If I purchase 10,000 bicycles, the unit price of the 10,000th bicycle would be 1.50
     * If I purchase 10,001 bicycles, the unit price of the 10,001st bicycle would be 1.00
     * If I purchase 100,001 bicycles, what would be the unit price of the 100,001st bicycle?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getUnitPriceTierAtQty(int $qty, array $tiers): float
    {
        $unitPrice = 0;
        $tier      = array_keys($tiers);
        $price     = array_values($tiers);

        if ( $qty > 0 ) {
            // looped thru all so we don't have to worry if someone changes the priceTiers
            for ($i=0; $i < count($tiers); $i++) {
                $current = $tier[$i];
                $next = next($tier);

                // this prevents $i+1 to return something more than the maximum amount of tiers 
                if ( $i === count($tiers)-1 || $qty >= $current && $qty < $next ) {
                    $unitPrice = $price[$i];
                    return $unitPrice; 
                }
            } 
        }
        return $unitPrice;
    }

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the total price of an order of items based on the quantity ordered
     *
     * Question:
     * If I purchase 10,000 bicycles, the total price would be 1.5 * 10,000 = $15,000
     * If I purchase 10,001 bicycles, the total price would be (1.5 * 10,000) + (1 * 1) = $15,001
     * If I purchase 100,001 bicycles, what would the total price be?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getTotalPriceTierAtQty(int $qty, array $tiers): float
    {
        $totalprice = 0;
        $tier  = array_keys($tiers);
        $price = array_values($tiers);
        $last  = count($tiers)-1;
        $maxTierPrice = [];
        
        // get maximum price of tiers below qty, get tier level of qty, add them tgt. 
        for ( $i=0; $i < count($tiers); $i++ ) { 
            $current = $tier[$i];
            $next    = next($tier);
            
            // save max price of first tier to maxTierPrice[] 
            if ( $i === 0 ) {
                $maxTierPrice[] = ( $next-1 ) * $price[$i];
            }
    
            // if iterating in middle, save the maximum price of the tiers to maxTierPrice[]  
            if ( $i > 0 && $i < $last ) {
                $maxTierPrice[] = (( $next-1 ) - ( $current-1 )) * $price[$i];
            }
    
            // return total price for qty that only reach the first tier, more than tier 0 but lesser than tier 1
            if ( $qty >= 0 && $qty < $tier[1] ) {
                $totalprice = $qty * $price[0];
                return $totalprice;
            }
    
            // return total price for qty for rest of tiers, $i = last condition to prevent $i+1 to return more than tier count
            if ( $i === $last || $qty >= $current && $qty < $next ) {
                $totalprice = (( $qty - ($current-1) ) * $price[$i]) + array_sum(array_slice($maxTierPrice, 0, $i));
                return $totalprice;
            } 
        }
    }

    /**
     * Task: Given an array of quantity of items ordered per month and an associative array of minimum order quantities and their respective prices, write a function to return an array of total charges incurred per month. Each item in the array should reflect the total amount the user has to pay for that month.
     *
     * Question A:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on a special pricing tier where the quantity does not reset each month and is thus CUMULATIVE.
     *
     * Question B:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on the typical pricing tier where the quantity RESETS each month and is thus NOT CUMULATIVE.
     *
     */
    public static function getPriceAtEachQty(array $qtyArr, array $tiers, bool $cumulative = false): array
    {
        // we will need the cumulated price and quantity
        $cumuPrice = [];
        $cumuQty = [];

        // get the maximum total price of each tier
        $tier1max = (array_keys($tiers)[1] - 1) * array_values($tiers)[0]; // 15000 = 10000 * 1.5
        $tier2max = (array_keys($tiers)[2] - 1) - (array_keys($tiers)[1] - 1) * array_values($tiers)[1]; // 90,000 = 100,000 - 10,000(max quantity for prev tier) * 1

        if ($cumulative) {
            foreach ($qtyArr as $qty) {
                $cumuQty[] = $qty; // create and sum up what has been accumulated

                if ( $qty <= 0 ) {
                    $price = 0;
                }
                // less than 10001 AND more than or equals to 0
                else if ( array_sum($cumuQty) < array_keys($tiers)[1] && array_sum($cumuQty) >= 0 ) {
                    $price = $qty * array_values($tiers)[0]; // 1.5
                }
                // less than 100,001 AND more than or equals to 10001
                else if ( array_sum($cumuQty) < array_keys($tiers)[2] && array_sum($cumuQty) >= array_keys($tiers)[1] ) {
                    
                    $qty = array_sum($cumuQty) - (array_keys($tiers)[1] - 1); // get remaining amount after deducting tier 1
                    $price = ($qty * array_values($tiers)[1]) + $tier1max - array_sum($cumuPrice); // qty * 1 + 15,000 - previous months prices
                }
                // more than 100,000
                else {
                    $qty = array_sum($cumuQty) - (array_keys($tiers)[2] - 1); // get remaining amount after deducting tier 2
                    $price = ($qty * array_values($tiers)[2]) + ($tier1max + $tier2max) - array_sum($cumuPrice); // qty * 0.5 + 105,000 - previous months prices
                }
                
                $cumuPrice[] = $price;
            }
        }
        else {
            foreach ($qtyArr as $qty) {
                if ( $qty <= 0 ) {
                    $price = 0;
                }
                // return total price for qty more than 0 AND less than 10,001
                else if ( $qty >= array_keys($tiers)[0] && $qty < array_keys($tiers)[1]) {
                    $price = $qty * array_values($tiers)[0];
                } 
                // return total price for qty more than 10,001 AND less than 100,001
                else if ( $qty >= array_keys($tiers)[1] && $qty < array_keys($tiers)[2]) {
                    $price = ( ($qty - (array_keys($tiers)[1] - 1) ) * array_values($tiers)[1] ) + $tier1max;
                }
                // return total price for qty more than 100,000
                else {
                    $price = ( ($qty - (array_keys($tiers)[2] - 1) ) * array_values($tiers)[2] ) + $tier1max + $tier2max;
                }

                $cumuPrice[] = $price;
            }
        }

        return $cumuPrice;
    }
}
