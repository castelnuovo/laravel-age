<?php

use Castelnuovo\LaravelAge\Age;
use Castelnuovo\LaravelAge\PrivateKey;
use Castelnuovo\LaravelAge\PublicKey;

it('can generate private key', function () {
    expect(PrivateKey::generate()->encode())->toBeString();
});

it('can generate public key', function () {
    $publicKey = PrivateKey::generate()->getPublicKey();

    expect($publicKey->encode())->toBeString();
});

it('can intialize with valid private key', function () {
    $encodeKey = 'AGE-SECRET-KEY-1TKGRTQP4H79MTNHVDRSA3L0CS7MFSMW0DQX80CWP0JDUFL97RRJSPK9777';
    $privateKey = new PrivateKey($encodeKey);

    expect($privateKey->encode())->toBe($encodeKey);
});

it('can intialize with valid public key', function () {
    $encodeKey = 'age1xqrfpqxz55ersvu6mmhwzcctqk27ppnatms7p9zruclrm8tt4y0q3apxuc';
    $publicKey = new PublicKey($encodeKey);

    expect($publicKey->encode())->toBe($encodeKey);
});

it('cannot intialize with invalid private key', function () {
    new PrivateKey('invalid');
})->throws(Exception::class);

it('cannot intialize with invalid public key', function () {
    new PublicKey('invalid');
})->throws(Exception::class);

it('can generate keypair', function () {
    $age = Age::generateKeypair();

    expect($age)->toBeInstanceOf(Age::class);
    expect($age->getPrivateKey())->toBeInstanceOf(PrivateKey::class);
    expect($age->getPublicKey())->toBeInstanceOf(PublicKey::class);
});

it('can encrypt with an provided private key', function () {
    $encodeKey = 'AGE-SECRET-KEY-1TKGRTQP4H79MTNHVDRSA3L0CS7MFSMW0DQX80CWP0JDUFL97RRJSPK9777';
    $privateKey = new PrivateKey($encodeKey);
    $age = new Age(privateKey: $privateKey);

    expect($age->encrypt('message'))->toBeString();
});

it('can encrypt with an provided public key', function () {
    $encodeKey = 'age1xqrfpqxz55ersvu6mmhwzcctqk27ppnatms7p9zruclrm8tt4y0q3apxuc';
    $publicKey = new PublicKey($encodeKey);
    $age = new Age(publicKey: $publicKey);

    expect($age->encrypt('message'))->toBeString();
});

it('can decrypt with an provided private key (using base64)', function () {
    $encodeKey = 'AGE-SECRET-KEY-1TKGRTQP4H79MTNHVDRSA3L0CS7MFSMW0DQX80CWP0JDUFL97RRJSPK9777';
    $privateKey = new PrivateKey($encodeKey);
    $age = new Age(privateKey: $privateKey);

    $message = 'Hello, World!';
    $encrypted = $age->encrypt($message);
    $decrypted = $age->decrypt($encrypted);

    expect($decrypted)->toBe($message);
});

it('can decrypt with an provided private key (without using base64)', function () {
    $encodeKey = 'AGE-SECRET-KEY-1TKGRTQP4H79MTNHVDRSA3L0CS7MFSMW0DQX80CWP0JDUFL97RRJSPK9777';
    $privateKey = new PrivateKey($encodeKey);
    $age = new Age(privateKey: $privateKey);

    $message = 'Hello, World!';
    $encrypted = $age->encrypt($message, false);
    $decrypted = $age->decrypt($encrypted, false);

    expect($decrypted)->toBe($message);
});

it('cannot decrypt with an provided public key', function () {
    $encodeKey = 'age1xqrfpqxz55ersvu6mmhwzcctqk27ppnatms7p9zruclrm8tt4y0q3apxuc';
    $publicKey = new PublicKey($encodeKey);
    $age = new Age(publicKey: $publicKey);

    $message = 'Hello, World!';
    $encrypted = $age->encrypt($message);
    $decrypted = $age->decrypt($encrypted);
})->throws(Exception::class);

it('cannot decrypt with an provided private key (using base64 only to encrypt)', function () {
    $encodeKey = 'age1xqrfpqxz55ersvu6mmhwzcctqk27ppnatms7p9zruclrm8tt4y0q3apxuc';
    $publicKey = new PublicKey($encodeKey);
    $age = new Age(publicKey: $publicKey);

    $message = 'Hello, World!';
    $encrypted = $age->encrypt($message);
    $decrypted = $age->decrypt($encrypted, false);
})->throws(Exception::class);

it('cannot decrypt with an provided private key (using base64 only to decrypt)', function () {
    $encodeKey = 'age1xqrfpqxz55ersvu6mmhwzcctqk27ppnatms7p9zruclrm8tt4y0q3apxuc';
    $publicKey = new PublicKey($encodeKey);
    $age = new Age(publicKey: $publicKey);

    $message = 'Hello, World!';
    $encrypted = $age->encrypt($message, false);
    $decrypted = $age->decrypt($encrypted);
})->throws(Exception::class);

it('runs usage code from readme', function () {
    $message = 'Hello World!';

    $age = Age::generateKeypair();
    $privateKey = $age->getPrivateKey();
    $publicKey = $age->getPublicKey();

    $age2 = new Age(publicKey: $publicKey);
    $encrypted_message = $age2->encrypt($message);

    $age3 = new Age(privateKey: $privateKey);
    $decrypted_message = $age3->decrypt($encrypted_message);

    expect($message)->toBe($decrypted_message);
});
