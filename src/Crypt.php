<?php

namespace Sim\Crypt;

use Sim\Crypt\Exceptions\CryptException;

class Crypt implements ICrypt
{
    /**
     * @see openssl_get_cipher_methods() - PHP build-in function, to see all methods
     */
    protected $encrypt_fst_method = 'aes-256-cbc';
    protected $encrypt_snd_method = 'sha3-512';

    /**
     * @var string $main_key
     */
    protected $main_key;

    /**
     * @var string $assured_key
     */
    protected $assured_key;

    /**
     * @var $has_error bool
     */
    protected $has_error = false;

    /**
     * Crypt constructor.
     * @param string $main_key - in Base64 type
     * @param string $assured_key - in Base64 type
     * @throws CryptException
     */
    public function __construct(string $main_key, string $assured_key)
    {
        // Check if crypt keys are ok
        if (empty($main_key) || empty($assured_key)) {
            throw new CryptException("You didn't specify [main_key] or [assured_key].");
        }

        $this->main_key = $main_key;
        $this->assured_key = $assured_key;
    }

    /**
     * @param string $first_method - see openssl_get_cipher_methods() for valid methods
     * @return static
     */
    public function setFirstEncryptionMethod(string $first_method)
    {
        $this->encrypt_fst_method = $first_method;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstEncryptionMethod(): string
    {
        return $this->encrypt_fst_method;
    }

    /**
     * @param string $second_method - see openssl_get_cipher_methods() for valid methods
     * @return static
     */
    public function setSecondEncryptionMethod(string $second_method)
    {
        $this->encrypt_snd_method = $second_method;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecondEncryptionMethod(): string
    {
        return $this->encrypt_snd_method;
    }

    /**
     * Encrypt the given $data
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): ?string
    {
        if ('' === $data) {
            $this->has_error = true;
            return null;
        }

        // Decode crypt keys
        $first_key = base64_decode($this->main_key);
        $second_key = base64_decode($this->assured_key);

        // Create an IV for encryption
        $iv_length = openssl_cipher_iv_length($this->encrypt_fst_method);
        $iv = openssl_random_pseudo_bytes($iv_length);

        // Encrypt for first time with first crypt key
        $first_encrypted = openssl_encrypt($data, $this->encrypt_fst_method, $first_key, OPENSSL_RAW_DATA, $iv);
        // Encrypt first encryption for second time with second crypt key
        $second_encrypted = hash_hmac($this->encrypt_snd_method, $first_encrypted, $second_key, TRUE);

        // Encode second encrypted data to base64 with created IV
        $output = base64_encode($iv . $second_encrypted . $first_encrypted);

        // Return the encrypted value
        $this->has_error = false;
        return $output;
    }

    /**
     * Decrypt the given $data.
     * Do exactly opposite of encryption
     *
     * @param string $data
     * @return mixed
     */
    public function decrypt(string $data): ?string
    {
        if ('' === $data) {
            $this->has_error = true;
            return null;
        }

        // Decode crypt keys
        $first_key = base64_decode($this->main_key);
        $second_key = base64_decode($this->assured_key);

        // Decode base64 coded $data
        $mix = base64_decode($data);

        // Get length of IV from crypt first method
        $iv_length = openssl_cipher_iv_length($this->encrypt_fst_method);

        // Get IV from decoded $data
        $iv = substr($mix, 0, $iv_length);
        // Get second encrypted $data from $mix
        $second_encrypted = substr($mix, $iv_length, 64);
        // Get first encrypted $data from $mix
        $first_encrypted = substr($mix, $iv_length + 64);

        // Decrypt first encrypted $data with first encryption method
        $data = openssl_decrypt($first_encrypted, $this->encrypt_fst_method, $first_key, OPENSSL_RAW_DATA, $iv);
        $second_encrypted_new = hash_hmac($this->encrypt_snd_method, $first_encrypted, $second_key, TRUE);

        // Check if new second encrypted data is equals to previous second encrypted data,
        // then return decrypted data
        if (hash_equals($second_encrypted, $second_encrypted_new)) {
            $this->has_error = false;
            return $data;
        }

        // Otherwise it has modified
        $this->has_error = true;
        return null;
    }

    /**
     * Use this function after each encryption or decryption to see everything is OK or not.
     * Note: Use this right after any encryption or decryption, because it'll change!
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->has_error;
    }
}