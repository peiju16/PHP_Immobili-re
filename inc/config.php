<?php

    $pdo = new PDO('mysql:host=localhost;dbname=immobilier', 'root', 'root', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ));


    // http://localhost:8080/PHP_APFA_2024/img/NOM_IMAGE.extension
    // j'ai déini une constante URL car j'ai besoin de récupérer la base en terme d'url de mon site
    // http://localhost:8080/PHP_APFA_2024
    // PK? parce que 'en bdd j'aurais besoin de stocker l'url d'ou se trouve mon image sur mon site
    // http://localhost:8080/PHP_APFA_2024/img/NOM_IMAGE.extension
    // $_SERVER => c'est une superglobale prédéfinie en PHP qui te donne des infos sur ton serveur
    // host, le dossier dans lequel tu trouves etc etc
    define("URL", "http://" . $_SERVER["HTTP_HOST"] . "/PHP_APFA_2024/01_TP/");

    // j'ai défini un constante RACINE_SITE car pour copier/colelr une image sur un serveur
    // j'ai besoin de le faire d'un dossier vers un autre
    // depuis le dossier temporaire de mon formulaire vers le dossier img de mon serveur
    define("RACINE_SITE", $_SERVER["DOCUMENT_ROOT"] . "/PHP_APFA_2024/01_TP/");

?>