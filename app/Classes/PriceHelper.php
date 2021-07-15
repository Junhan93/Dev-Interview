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
        if ( $qty <= 0) {
            $unitPrice = 0;
        }
        // return price tier if qty is more than 0 AND less than 10,001
        else if ( $qty >= array_keys($tiers)[0] && $qty < array_keys($tiers)[1]) {
            $unitPrice = array_values($tiers)[0];
        } 
        // return price tier if qty is more than or equals to 10,001 AND less than 100,001
        else if ( $qty >= array_keys($tiers)[1] && $qty < array_keys($tiers)[2]) {
            $unitPrice = array_values($tiers)[1];
        }
        // return price tier if qty is more than or equals to 100,001
        else if ( $qty >= array_keys($tiers)[2] ) {
            $unitPrice = array_values($tiers)[2];
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
        // get the maximum total price of each tier
        $tier1max = (array_keys($tiers)[1] - 1) * array_values($tiers)[0]; // 15000 = 10000 * 1.5
        $tier2max = (array_keys($tiers)[2] - 1) - (array_keys($tiers)[1] - 1) * array_values($tiers)[1]; // 90,000 = 100,000 - 10,000(max quantity for prev tier) * 1

        if ( $qty <= 0) {
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
        // return total price for qty more than or equals to 100,001
        else if ( $qty >= array_keys($tiers)[2] ) {
            $price = ( ($qty - (array_keys($tiers)[2] - 1) ) * array_values($tiers)[2] ) + $tier1max + $tier2max;
        }

        return $price;
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
       return [];
    }
}
