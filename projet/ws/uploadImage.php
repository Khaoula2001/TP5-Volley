<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Méthode non autorisée"]);
    exit;
}


$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['image'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Données invalides ou image manquante"]);
    exit;
}

$imageData = $data['image'];
$prefix = 'data:image/';

if (strpos($imageData, $prefix) !== 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Format d'image non supporté"]);
    exit;
}


$mimeType = substr($imageData, strpos($imageData, ':') + 1, strpos($imageData, ';') - strpos($imageData, ':') - 1);
$base64Data = substr($imageData, strpos($imageData, ',') + 1);


$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($mimeType, $allowedMimeTypes)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Type d'image non supporté"]);
    exit;
}


$decoded = base64_decode($base64Data, true);
if ($decoded === false) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Données image corrompues"]);
    exit;
}

if (strlen($decoded) > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "L'image est trop volumineuse (max 5MB)"]);
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Impossible de créer le dossier de destination"]);
    exit;
}

$extension = 'jpg';
if ($mimeType === 'image/png')
    $extension = 'png';
if ($mimeType === 'image/gif')
    $extension = 'gif';

$filename = uniqid('img_') . '.' . $extension;
$filepath = $uploadDir . $filename;


if (file_put_contents($filepath, $decoded) === false) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Échec de l'enregistrement de l'image"]);
    exit;
}

file_put_contents('upload_log.txt', print_r([
    "status" => "success",
    "url" => "http://10.0.2.2/projet/ws/uploads/" . $filename,
    "filename" => $filename
], true), FILE_APPEND);


echo json_encode([
    "status" => "success",
    "url" => "http://10.0.2.2/projet/ws/uploads/" . $filename,
    "filename" => $filename,
    "size" => strlen($decoded),
    "mimeType" => $mimeType
], JSON_PRETTY_PRINT);
