<?php
require __DIR__ . '/vendor/autoload.php';

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use Dotenv\Dotenv;

// Carrega o .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configura o token
MercadoPagoConfig::setAccessToken($_ENV['MERCADO_PAGO_ACCESS_TOKEN']);

$client = new PreferenceClient();

// Cria preferência
$preference = $client->create([
    "items" => [
        [
            "title" => "Meu Produto",
            "quantity" => 1,
            "currency_id" => "BRL",
            "unit_price" => 100.0
        ]
    ],
    "back_urls" => [
        "success" => "https://meusite.com/sucesso.html",
        "failure" => "https://meusite.com/falha.html",
        "pending" => "https://meusite.com/pendente.html"
    ],
    "auto_return" => "approved"
]);

echo json_encode(["init_point" => $preference->init_point]);
