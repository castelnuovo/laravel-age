<?php

namespace Castelnuovo\LaravelAge;

use Exception;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\ExecutableFinder;

class PrivateKey
{
    private string $privateKey;

    public function __construct(string $privateKey = '')
    {
        if (!$privateKey) {
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

        if (!$privateKey->startsWith('AGE-SECRET-KEY-') || $privateKey->length() !== 74) {
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
        $dir = TemporaryDirectory::make()->deleteWhenDestroyed();

        $data = $base64 ? base64_decode(str_replace(['-', '_'], ['+', '/'], $message)) : $message;
        Storage::build(['driver' => 'local', 'root' => $dir->path()])->put($ulid, $data);

        /**
         * @var array<string>|string|null
         */
        $command = [
            (new ExecutableFinder())->find('age', 'age', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            '-d',
            '-i',
            '-',
            $dir->path($ulid),
        ];

        $result = Process::input($this->encode())->run($command);

        if ($result->failed()) {
            throw new Exception('Failed to decrypt message!');
        }

        return $result->output();
    }
}
