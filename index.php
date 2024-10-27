<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();

$api_key = "yyBVA2hBKu9V5IHfD2iIduXjsEBqaG6o";
$api_secret = "iXCppJyFNYqA7Et5";

// Passo 1: Obter o Token de Acesso
$response = $client->post('https://test.api.amadeus.com/v1/security/oauth2/token', [
    'form_params' => [
        'grant_type' => 'client_credentials',
        'client_id' => $api_key,
        'client_secret' => $api_secret,
    ]
]);

$body = json_decode($response->getBody(), true);
$access_token = $body['access_token'] ?? null;

if ($access_token) {
    // Passo 2: Fazer a Requisição para Buscar Voos
    $origin = "LIS";
    $destination = "MAD";
    $departureDate = "2024-12-01";

    $response = $client->get("https://test.api.amadeus.com/v2/shopping/flight-offers", [
        'headers' => [
            'Authorization' => "Bearer $access_token",
            'Accept-Encoding' => 'gzip',
            'Content-Type' => 'application/json'
        ],
        'query' => [
            'originLocationCode' => $origin,
            'destinationLocationCode' => $destination,
            'departureDate' => $departureDate,
            'adults' => 1,
        ]
    ]);

    // Verificar o cabeçalho "Content-Encoding" para decidir sobre a descompressão
    $contentEncoding = $response->getHeaderLine('Content-Encoding');
    
    if ($contentEncoding === 'gzip') {
        // Decodificar se a resposta está realmente em Gzip
        $data = json_decode(gzdecode($response->getBody()->getContents()), true);
    } else {
        // Se não estiver em Gzip, processar normalmente
        $data = json_decode($response->getBody()->getContents(), true);
    }
    
    echo "Voos Encontrados: \n";
    echo "<pre>";
    print_r($data);
echo "</pre>";
} else {
    echo "Erro ao obter token de autenticação.";
}
?>
