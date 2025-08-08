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

// Recebe o conteúdo do POST como JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verifica se os dados foram recebidos corretamente
if (!$data || !isset($data['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados de itens inválidos.']);
    exit;
}

// Mapeia os itens do carrinho para o formato do Mercado Pago
$mpItems = array_map(function ($item) {
    return [
        "title" => $item['name'],
        "quantity" => $item['quantity'],
        "currency_id" => "BRL",
        "unit_price" => (float) $item['price']
    ];
}, $data['items']);

// Cria a preferência de pagamento com os itens dinâmicos
try {
    $preference = $client->create([
        "items" => $mpItems,
        "back_urls" => [
            "success" => "https://meusite.com/sucesso.html",
            "failure" => "https://meusite.com/falha.html",
            "pending" => "https://meusite.com/pendente.html"
        ],
        "auto_return" => "approved"
    ]);

    // Retorna o link de pagamento
    echo json_encode(["init_point" => $preference->init_point]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    // Opcionalmente, você pode registrar o erro em um log para depuração
    error_log("Erro ao criar preferência do Mercado Pago: " . $e->getMessage());
}
