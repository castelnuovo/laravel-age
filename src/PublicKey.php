<?php

namespace Castelnuovo\LaravelAge;

use Exception;
use Illuminate\Support\Facades\Process;

class PublicKey
{
    private string $publicKey;

    public function __construct(string $publicKey)
    {
        $publicKey = str($publicKey)->trim();

        if (! $publicKey->startsWith('age') || $publicKey->length() !== 62) {
            throw new Exception('Invalid public key provided!');
        }

        $this->publicKey = $publicKey;
    }

    public function encode(): string
    {
        return str($this->publicKey)->trim();
    }

    /**
     * Encrypt a message using the public key.
     * Returns the base64 encoded encrypted message.
     */
    public function encrypt(string $message, bool $base64): string
    {
        $result = Process::input($message)->run("age -r {$this->encode()}");

        if ($result->failed()) {
            throw new Exception('Failed to encrypt message!');
        }

        return $base64 ? base64_encode($result->output()) : $result->output();
    }
}
