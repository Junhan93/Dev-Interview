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
                $currentTier = $tier[$i];
                $nextTier = next($tier);

                // this prevents $i+1 to return something more than the maximum amount of tiers 
                if ( $i === count($tiers)-1 || $qty >= $currentTier && $qty < $nextTier ) {
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
            $currentTier = $tier[$i];
            $nextTier    = next($tier);
            
            // save max price of first tier to maxTierPrice[] 
            if ( $i === 0 ) {
                $maxTierPrice[] = ( $nextTier-1 ) * $price[$i];
            }
    
            // if iterating in middle, save the maximum price of the tiers to maxTierPrice[]  
            if ( $i > 0 && $i < $last ) {
                $maxTierPrice[] = (( $nextTier-1 ) - ( $currentTier-1 )) * $price[$i];
            }
    
            // return total price for qty that only reach the first tier, more than tier 0 but lesser than tier 1
            if ( $qty >= 0 && $qty < $tier[1] ) {
                $totalprice = $qty * $price[0];
                return $totalprice;
            }
    
            // return total price for qty for rest of tiers, $i = last condition to prevent $i+1 to return more than tier count
            if ( $i === $last || $qty >= $currentTier && $qty < $nextTier ) {
                $totalprice = (( $qty - ($currentTier-1) ) * $price[$i]) + array_sum(array_slice($maxTierPrice, 0, $i));
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
        $cumuPrice = [];
        $maxTierPrice = []; 

        $totalprice = 0;
        $tier  = array_keys($tiers);
        $price = array_values($tiers);
        $last  = count($tiers)-1;

        // loop thru all quantities
        foreach($qtyArr as $qty) {
            // add into cumulating quantity
            $cumuQty[] = $qty;
            $totalquantity = array_sum($cumuQty);
            
            // for each quantity, deduct from total quantities, 
            for ( $i=0; $i < count($tiers); $i++ ) { 
                $currentTier = $tier[$i];
                $nextTier    = next($tier);
                
                // separated the first tier because no deduction needed 
                if ( $i === 0 ) {
                    $maxTierPrice[] = ( $nextTier-1 ) * $price[$i];
                }
                // second tier onwards, we will need to minus off previous tier's max
                if ( $i > 0 && $i < $last ) {
                    $maxTierPrice[] = (( $nextTier-1 ) - ( $currentTier-1 )) * $price[$i];
                }

                // calculate cumulative every month
                if ($cumulative) {
                    if ( $totalquantity >= 0 && $totalquantity < $tier[1] ) {
                        $qty = $totalquantity;
                        $totalprice = $qty * $price[0];
                    }

                    if ( $totalquantity >= $tier[ $last ] || $totalquantity >= $currentTier && $totalquantity < $tier[$i+1] ) {
                        $qty = $totalquantity - ($currentTier-1);
                        $totalprice = ( $qty * $price[$i] ) + array_sum(array_slice($maxTierPrice, 0, $i)) - array_sum($cumuPrice);
                    } 
                }
    
                // reset every month
                else {
                    if ( $qty >= 0 && $qty < $tier[1] ) {
                        $totalprice = $qty * $price[0];
                    }
    
                    if ( $qty >= $tier[ $last ] || $qty >= $currentTier && $qty < $tier[$i+1] ) {
                        $totalprice = (( $qty - ($currentTier-1) ) * $price[$i]) + array_sum(array_slice($maxTierPrice, 0, $i));
                    } 
                }
            }
            
            $cumuPrice[] = $totalprice;
        }
        return $cumuPrice;   
    } 
}
