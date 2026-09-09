<?php

it('adds browser security policy headers', function () {
    $response = $this->get('/');

    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader(
        'Permissions-Policy',
        'accelerometer=(), camera=(), display-capture=(), fullscreen=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), picture-in-picture=(), usb=()',
    );
});
