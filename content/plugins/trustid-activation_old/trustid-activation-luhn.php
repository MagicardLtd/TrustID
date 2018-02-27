<?php

// Functions for computing and checking arbitrary-base Luhn checksums
// Input must be provided as an array of integers representing the digits (in correct base)
// with indices of (0..n-1)


/*int*/ function GetLuhnChecksum(/*array*/ $digits, /*int*/ $base = 10)
{
	return ($base - LuhnSum($digits, $base, true)) % $base;
}

/*bool*/ function CheckLuhnChecksum(/*array*/ $digits, /*int*/ $base = 10)
{
	return LuhnSum($digits, $base) == 0;
}


/*int*/ function LuhnSum(/*array*/ $digits, /*int*/ $base = 10, /*bool*/ $doubleLast = false)
{
	$result = 0;
	$dbl = $doubleLast;
	for ($i = count($digits)-1; $i >= 0; --$i)
	{
		if ($dbl)
		{
			$dblDigit = $digits[$i] * 2;
			if ($dblDigit >= $base)	$result += ($dblDigit % $base) + 1;
			else                   	$result += $dblDigit;
		}
		else
		{
			$result += $digits[$i];
		}
		$dbl = !$dbl;
	}
	$result %= $base;
	return $result;
}
