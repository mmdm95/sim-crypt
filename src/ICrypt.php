<?php

namespace Sim\Crypt;


interface ICrypt
{
    /**
     * Encrypt the given $data
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string;

    /**
     * Decrypt the given $data
     *
     * @param string $data
     * @return string
     */
    public function decrypt(string $data): string;

    /**
     * Return true if encryption/decryption has error in it
     * otherwise return false
     *
     * @return bool
     */
    public function hasError(): bool;
}