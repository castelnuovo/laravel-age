<?php

namespace Castelnuovo\LaravelAge;

use Exception;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrivateKey
{
    private string $privateKey;

    public function __construct(string $privateKey = '')
    {
        if (! $privateKey) {
            $result = Process::pipe([
                'age-keygen',
                'grep -E "^AGE-SECRET-KEY-[A-Za-z0-9]{59}$"',
            ]);

            if ($result->failed()) {
                throw new Exception('Failed to generate private key!');
            }

            $privateKey = $result->output();
        }

        $privateKey = str($privateKey)->trim();

        if (! $privateKey->startsWith('AGE-SECRET-KEY-') || $privateKey->length() !== 74) {
            throw new Exception('Invalid private key provided!');
        }

        $this->privateKey = $privateKey;
    }

    public function encode(): string
    {
        return $this->privateKey;
    }

    public function getPublicKey(): PublicKey
    {
        $result = Process::input($this->encode())->run('age-keygen -y');

        if ($result->failed()) {
            throw new Exception('Failed to generate public key!');
        }

        return new PublicKey($result->output());
    }

    /**
     * Decrypt a base64 encoded message using the private key.
     * Returns the decrypted message.
     */
    public function decrypt(string $message, bool $base64): string
    {
        $ulid = Str::ulid();
        Storage::put($ulid, $base64 ? base64_decode($message) : $message);

        $path = Storage::path($ulid);
        $result = Process::input($this->encode())->run("age -d -i - {$path}");

        Storage::delete($ulid);

        if ($result->failed()) {
            throw new Exception('Failed to decrypt message!');
        }

        return $result->output();
    }
}
