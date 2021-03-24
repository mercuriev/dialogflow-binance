<?php

define('SATOSHI', 100000000);

function get_class_short(object $object)
{
    return array_reverse(explode('\\', get_class($object)))[0];
}

/**
 * Works as class_uses() except also return traits used by traits and parent classes.
 *
 * @param string $class
 * @param string $autoload
 * @return array
 */
function class_uses_recursive($class, $autoload = true)
{
    $traits = [];

    // Get traits of all parent classes
    do {
        $traits = array_merge(class_uses($class, $autoload), $traits);
    } while ($class = get_parent_class($class));

    // Get traits of all parent traits
    $traitsToSearch = $traits;
    while (!empty($traitsToSearch)) {
        $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
        $traits = array_merge($newTraits, $traits);
        $traitsToSearch = array_merge($newTraits, $traitsToSearch);
    };

    foreach ($traits as $trait => $same) {
        $traits = array_merge(class_uses($trait, $autoload), $traits);
    }

    return array_unique($traits);
}

/**
 * Return pretty printed human readable JSON.
 *
 * @param array $data
 */
function json_encode_pretty($data)
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function truncate($float, $decimals = 2)
{
    $power = pow(10, $decimals);
    if($float > 0){
        return floor($float * $power) / $power;
    } else {
        return ceil($float * $power) / $power;
    }
}

// Generates a strong password of N length containing at least one lower case letter,
// one uppercase letter, one digit, and one special character. The remaining characters
// in the password are chosen at random from those four sets.
//
// The available characters in each set are user friendly - there are no ambiguous
// characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
// makes it much easier for users to manually type or speak their passwords.
//
// Note: the $add_dashes option will increase the length of the password by
// floor(sqrt(N)) characters.
function pwgen($length = 9, $add_dashes = false, $available_sets = 'luds')
{
    $sets = array();
    if(strpos($available_sets, 'l') !== false)
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    if(strpos($available_sets, 'u') !== false)
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    if(strpos($available_sets, 'd') !== false)
        $sets[] = '23456789';
    if(strpos($available_sets, 's') !== false)
        $sets[] = '!@#$%&*?';

    $all = '';
    $password = '';
    foreach($sets as $set)
    {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }

    $all = str_split($all);
    for($i = 0; $i < $length - count($sets); $i++)
        $password .= $all[array_rand($all)];

    $password = str_shuffle($password);

    if(!$add_dashes)
        return $password;

    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while(strlen($password) > $dash_len)
    {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }
    $dash_str .= $password;
    return $dash_str;
}

function uuid()
{
    $bytes    = random_bytes(16);
	$bytes[6] = chr((ord($bytes[6]) & 0b00001111) | 0b01000000);
	$bytes[8] = chr((ord($bytes[8]) & 0b00111111) | 0b10000000);
    $hex = bin2hex($bytes);

	return substr($hex, 0, 8) . '-' .
	       substr($hex, 8, 4) . '-' .
	       substr($hex, 12, 4) . '-' .
	       substr($hex, 16, 4) . '-' .
	       substr($hex, 20);

}

function utfize($val)
{
    if (is_array($val)) {
        foreach ($val as $k => $v) {
            $val[$k] = utfize($v);
        }
    } else if (is_string($val)) {
        $val = mb_convert_encoding($val, 'UTF-8', 'UTF-8');
    }
    return $val;
}
