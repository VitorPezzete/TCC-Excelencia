<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mercado Pago Credentials
    |--------------------------------------------------------------------------
    | Configure suas credenciais no arquivo .env:
    |   MP_ACCESS_TOKEN=TEST-xxxx   (token de acesso da sua conta MP)
    |   MP_PUBLIC_KEY=TEST-xxxx     (chave pública para o frontend)
    |
    | Acesse: https://www.mercadopago.com.br/developers/panel/app
    */

    'access_token' => env('MP_ACCESS_TOKEN', ''),
    'public_key'   => env('MP_PUBLIC_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | PIX Expiration (em minutos)
    |--------------------------------------------------------------------------
    | Tempo de expiração do QR Code PIX gerado.
    | O Mercado Pago aceita de 5 a 600 minutos.
    */
    'pix_expiration_minutes' => env('MP_PIX_EXPIRATION_MINUTES', 30),
];
