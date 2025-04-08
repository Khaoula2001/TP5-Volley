<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include_once '../racine.php';
include_once RACINE . '/service/EtudiantService.php';

try {
    $es = new EtudiantService();
    $etudiants = $es->findAllApi();

    echo json_encode([
        'status' => 'success',
        'data' => $etudiants,
        'count' => count($etudiants),
        'timestamp' => time()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>

