<?php

include_once '../racine.php';
include_once RACINE . '/service/EtudiantService.php';
extract($_GET);
echo $nom;
$es = new EtudiantService();
$es->create(new Etudiant(1, $nom, $prenom, $ville, $sexe));
header("location:../index.php");


