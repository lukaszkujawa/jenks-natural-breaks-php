<?php

/**
 *
 *	Jenks natural breaks optimization - http://en.wikipedia.org/wiki/Jenks_natural_breaks_optimization
 *  
 *	This is PHP port of https://gist.github.com/tmcw/4977508 by Tom MacWright
 *
 *	Usage:
 *		$data = array(1,2,3,4,10,24,26,100,101);
 *		print_r( Jenks::getBreaks( $data, 4 ) );
 *
 */

class Jenks {
	
	static private function getMatrices($data, $numClasses) {
		$lowerClassLimits = array();
		$varianceCombinations = array();

		$i = 0;
		$j = 0;
		$variance = 0;

		
		for($i = 0 ; $i < sizeof( $data ) + 1 ; $i++) {
			$tmp1 = array(); 
			$tmp2 = array();

			for ($j = 0 ; $j < $numClasses + 1 ; $j++) {
				$tmp1[] = 0;
				$tmp2[] = 0;
			}

			$lowerClassLimits[] = $tmp1;
			$varianceCombinations[] = $tmp2;
		}

		for($i = 1 ; $i < $numClasses + 1 ; $i++) {
			$lowerClassLimits[ 1 ][ $i ] = 1;
			$varianceCombinations[ 1 ][ $i ] = 0;

			for ($j = 2; $j < sizeof( $data ) + 1; $j++) {
				$varianceCombinations[ $j ][ $i ] = 0xFFFFFFFF;
			}
		}

		for($l = 2; $l < sizeof( $data ) + 1; $l++) {

			$sum = 0;
			$sumSquares = 0;
			$w = 0;
			$i4 = 0;

			for($m = 1 ; $m < $l + 1 ; $m++) {
				$lowerClassLimit = $l - $m + 1;
				$val = $data[ $lowerClassLimit - 1 ];

				$w++;

				$sum += $val;
				$sumSquares += $val * $val;

				$variance = $sumSquares - ($sum * $sum) / $w;
				$i4 = $lowerClassLimit - 1;

				if($i4 !== 0) {
					for ($j = 2; $j < $numClasses + 1; $j++) {
						if ($varianceCombinations[ $l ][ $j ] >= ( $variance + $varianceCombinations[ $i4 ][ $j - 1 ] ) ) {
							$lowerClassLimits[ $l ][ $j ] = $lowerClassLimit;
							$varianceCombinations[ $l ][ $j ] = $variance + $varianceCombinations[ $i4 ][ $j - 1 ];
						}
					}
				}
			}

			$lowerClassLimits[ $l ][ 1 ] = 1;
			$varianceCombinations[ $l ][ 1 ] = $variance;
		}

		return array(
			'lowerClassLimits' => $lowerClassLimits,
			'varianceCombinations' => $varianceCombinations
        );            

	}

	static private function _getBreaks($data, $lowerClassLimits, $numClasses) {
		$k = sizeof( $data ) - 1;
		$kclass = array_fill(0, $numClasses + 1, 0);
		$countNum = $numClasses;

		$kclass[ $numClasses] = $data[ sizeof( $data ) - 1 ];
		$kclass[ 0 ] = $data[ 0 ];

		while( $countNum > 1 ) {
			$kclass[ $countNum - 1 ] = $data[ $lowerClassLimits[ $k ][ $countNum ] - 2 ];
			$k = $lowerClassLimits[ $k ][ $countNum ] - 1;
			$countNum--;
		}
 
		return $kclass;
	}

	static public function getBreaks($data, $numClasses) {
		if( $numClasses > sizeof( $data ) ) {
			return null;
		}

		sort( $data );

		$matrices = self::getMatrices($data, $numClasses);
		$lowerClassLimits = $matrices[ 'lowerClassLimits' ];

		return self::_getBreaks($data, $lowerClassLimits, $numClasses);
	}

}
