<?php
// String
function oc_strlen(string $string) {
	return mb_strlen($string);
}

function oc_strpos(string $string, string $needle, int $offset = 0) {
	return mb_strpos($string, $needle, $offset);
}

function oc_strrpos(string $string, string $needle, int $offset = 0) {
	return mb_strrpos($string, $needle, $offset);
}

function oc_substr(string $string, int $offset, ?int $length = null) {
	return mb_substr($string, $offset, $length);
}

function oc_strtoupper(string $string) {
	return mb_strtoupper($string);
}

function oc_strtolower(string $string) {
	return mb_strtolower($string);
}

// Other
function oc_token(int $length = 32): string {
	return substr(bin2hex(random_bytes($length)), 0, $length);
}

/**
 * Format a street/mailing address for display, normalizing line breaks to
 * single newlines.
 *
 * Supported placeholders for address components are:
 *
 *		{firstname}
 *		{lastname}
 *		{company}
 *		{address_1}
 *		{address_2}
 *		{city}
 *		{postcode}
 *		{zone}
 *		{zone_code}
 *		{country}
 *
 * @param array  $address_info The data fields of the address keyed by the
 *                             placeholders used in the format string.
 *
 * @param string $format       Address format string containing placeholders to
 *                             be replaced by the corresponding $address_info
 *                             values.  Optional, defaults to a basic format
 *                             string if not present, NULL, or an empty string.
 *
 * @return string The address as a string formatted according to the supplied
 *                format string.
 */
function oc_format_address(array $address_info, string $format = null): string {
	$placeholders = [
		'{firstname}',
		'{lastname}',
		'{company}',
		'{address_1}',
		'{address_2}',
		'{city}',
		'{postcode}',
		'{zone}',
		'{zone_code}',
		'{country}'
	];

	// Scrub invalid entries from $address_info and default missing entries to ''
	$address = array_fill_keys($placeholders, '');
	$address = array_merge($address, array_intersect_key($address_info, $address));

	if (!$format) {
		// Use default format
		$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
	}

	// After expanding the placeholders in the address format, replace all line
	// breaks and any surrounding whitespace with a single newline, then
	// collapse any multiple whitespace character sequences remaining in each
	// line into a single space
	return preg_replace(['/\s*\R\s*/', '/\s\s+/m'], ["\n", ' '],
											trim(str_replace(array_keys($address),
																			 array_values($address), $format)));
}
