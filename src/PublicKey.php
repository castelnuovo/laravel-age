<?php

namespace Castelnuovo\LaravelAge;

use Exception;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\ExecutableFinder;

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
        /**
         * @var array<string>|string|null
         */
        $command = [
            (new ExecutableFinder())->find('age', 'age', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            '-r',
            $this->encode(),
        ];

        $result = Process::input($message)->run($command);

        if ($result->failed()) {
            throw new Exception('Failed to encrypt message!');
        }

        return $base64 ? base64_encode($result->output()) : $result->output();
    }
}
