<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");


error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);


include_once '../racine.php';
include_once RACINE . '/service/EtudiantService.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';

        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
        } else {
            $data = $_POST;
        }


        $es = new EtudiantService();
        $etudiant = new Etudiant(
            1,
            $data['nom'],
            $data['prenom'],
            $data['ville'],
            $data['sexe'],
            $data['photoUrl'] ?? '',

        );

        $es->create($etudiant);


        echo json_encode([
            "status" => "success",
            "message" => "Étudiant créé avec succès"
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Méthode non autorisée"
    ]);
}
