# Simplicity Crypt
A library to encrypt/decrypt your data with two keys and two 
cipher methods.

## Install
**composer**
```php 
composer require mmdm/sim-crypt
```

Or you can simply download zip file from github and extract it, 
then put file to your project library and use it like other libraries.

Just add line below to autoload files:

```php
require_once 'path_to_library/autoloader.php';
```

and you are good to go.

## How to use
```php
// to instance a crypt object
$crypt = new Crypt($main_key, $assured_key);

// to crypt data
$encrypted_message = $crypt->encrypt("message to encrypt");

// to encrypt data
$decrypted_message = $crypt->decrypt($encrypted_message);

// "message to encrypt" will be equal to $decrypted_message at the end
```

### Description

To instantiate crypt object, you need two keys. One is the main key 
and the other is assured key. These keys must be base64 coded strings.
You can build them using [this website](https://mypwd.net/). First 
generate strong passwords then use base64 encoder/decoder to make 
them as base64 coded strings and use them for library **or** you 
can choose two strings as password and use php function `base64_encode()` 
to make them base64.

## Available functions

`setFirstEncryptionMethod(string $first_method)`

You can set first encryption method from valid encryption methods. 
See `openssl_get_cipher_methods()` PHP built-in function for 
supported methods.

`getFirstEncryptionMethod(): string`

Get first encryption method.

`setSecondEncryptionMethod(string $second_method)`

You can set second encryption method from valid encryption methods. 
See `openssl_get_cipher_methods()` PHP built-in function for 
supported methods.

`getSecondEncryptionMethod(): string`

Get second encryption method.

`encrypt($data)`

This method encrypts a message and return encrypted value or 
false if $data is empty or is not a string

```php
$encrypted_message = $crypt->encrypt($message_to_encrypt);
```

`decrypt($data)`

This method decrypts an encrypted message and return actual 
message or false if $data is empty or is not a string or an error 
happened during decode step.

```php
$decrypted_message = $crypt->decrypt($encrypted_message);
``` 

`hasError()`

This function return a boolean indicates operation has error or not.

Note: This method should call after encrypt or decrypt methods to 
take action on error occurrence.

```php
$bool = $crypt->hasError(); // true on having error or false on OK
```

# License
Under MIT license.
