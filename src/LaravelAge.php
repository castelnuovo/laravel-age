<?php

namespace Castelnuovo\LaravelAge;

use Exception;

class LaravelAge
{
    public function __construct(private ?PrivateKey $privateKey = null, private ?PublicKey $publicKey = null)
    {
        /* If no keys are provided, use the one from .env or create a pair. */
        if (! isset($privateKey) && ! isset($publicKey)) {
            /** @phpstan-ignore-next-line */
            $this->privateKey = new PrivateKey(config('laravel-age.identity'));
            $this->publicKey = $this->privateKey->getPublicKey();
        }

        /* If only the private key is provided, generate the public key. */
        if (isset($privateKey) && ! isset($publicKey)) {
            $this->publicKey = $privateKey->getPublicKey();
        }
    }

    public static function generateKeypair(): LaravelAge
    {
        $privateKey = new PrivateKey();

        return new LaravelAge($privateKey, $privateKey->getPublicKey());
    }

    public function getPublicKey(): PublicKey
    {
        if (! isset($this->publicKey)) {
            throw new Exception('Public key not set!');
        }

        return $this->publicKey;
    }

    public function getPrivateKey(): PrivateKey
    {
        if (! isset($this->privateKey)) {
            throw new Exception('Private key not set!');
        }

        return $this->privateKey;
    }

    /**
     * Encrypt a message using the public key.
     * Returns the base64 encoded encrypted message.
     */
    public function encrypt(string $message, bool $base64 = true): string
    {
        if (! isset($this->publicKey)) {
            throw new Exception('Public key not set!');
        }

        return $this->publicKey->encrypt($message, $base64);
    }

    /**
     * Decrypt a base64 encoded message using the private key.
     * Returns the decrypted message.
     */
    public function decrypt(string $message, bool $base64 = true): string
    {
        if (! isset($this->privateKey)) {
            throw new Exception('Private key not set!');
        }

        return $this->privateKey->decrypt($message, $base64);
    }
}
