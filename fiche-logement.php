<?php

    require_once("inc/config.php");

    // si j'ai un paramètre GET id_logement 
    if(isset($_GET["id_logement"])) {
        $id = $_GET["id_logement"];
        // je récupère ses infos en BDD
        $objStmt = $pdo->query("SELECT * FROM logement WHERE id_logement = '$id' ");
        $logement = $objStmt->fetch(PDO::FETCH_ASSOC);
        
    } else {
        // je redirige
        header("location:liste-logements.php");
    }

    require_once("inc/haut_page.php");

?>


    <div class="card" style="width: 18rem;">
    <img src="<?= $logement["photo"]; ?>" class="card-img-top" alt="<?= $logement["description"]; ?>">
    <div class="card-body">
        <h5 class="card-title"><?= $logement["titre"]; ?></h5>
        <p class="card-text"><?= $logement["description"]; ?></p>
        <p class="card-text"><?= $logement["prix"]; ?></p>
        <p class="card-text"><?= $logement["type"]; ?></p>
        <p class="card-text"><?= $logement["adresse"]; ?></p>
        <p class="card-text"><?= $logement["cp"]; ?></p>
        <p class="card-text"><?= $logement["ville"]; ?></p>
        <a href="index.php?id_logement=<?= $logement["id_logement"]; ?>&action=modification" class="btn btn-primary">Go somewhere</a>
    </div>
    </div>



<?php

    require_once("inc/bas_page.php");

?>