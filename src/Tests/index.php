<?php

use Sim\Crypt\Crypt;
use Sim\Crypt\Exceptions\CryptException;

include_once '../../vendor/autoload.php';
//include_once '../../autoloader.php';

$main_key = 'fDhIL1dmU2swMyl+VEUxR3gkJWRJO0RQNUxRUks2aFZZKDJsOVhVYzdCNE52eiEreU9fPkA=';
$assured_key = 'eCtYfHRDOFVsOSV6aTZBNyk6Lyg+MGc0MTI8NTNKTXk=';

try {
    $crypt = new Crypt($main_key, $assured_key);
    $message = 'it is a message';
    $encrypted_message = $crypt->encrypt($message);
    $decrypted_message = $crypt->decrypt($encrypted_message);

    echo 'Are $message and $decrypted_message equal? ';
    if($message == $decrypted_message) echo 'YES, and both show [' . $decrypted_message . ']'; else echo 'NO!';
    echo "\nHave any error then? ";
    if($crypt->hasError()) echo 'Ohh noo! We have error'; else echo 'No, everything is fine :)';
} catch (CryptException $e) {
}
