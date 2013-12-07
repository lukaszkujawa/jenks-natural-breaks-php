<?php

/**
 *
 *	$ php example01.php
 *	From: 19 To: 26 (9)
 *	From: 40 To: 40 (7)
 *	From: 80 To: 83 (3)
 *	From: 100 To: 101 (2)
 *
 */

require "Jenks.php";

$_prices = array(
	19, 19, 20, 20, 20, 25, 25, 25, 26, 40, 40, 40, 40, 40, 40, 40, 80, 82, 83, 100, 101
);

$breaks = Jenks::getBreaks( $_prices, 4 );

$cls = 1;
$from = $_prices[ 0 ];
$prices = array_unique( $_prices );
sort( $prices );

foreach( $prices as $i => $price ) {
	if( $price >= $breaks[ $cls ] ) {
		$count = 0;
		foreach( $_prices as $p ) {
			if( $p >= $from && $p <= $price ) {
				$count++;
			}
		}
		printf( "From: %s To: %s (%s)\n", $from, $price, $count );
		if( isset( $prices[ $i + 1 ] ) ) {
			$from = $prices[ $i + 1 ];
		}

		$cls++;
	}
}