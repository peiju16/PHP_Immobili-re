<?php

require_once("inc/config.php");

// variabiliser le contenu pour les msg à afficher
$content = "";

// si j'ai un paramètre get action dans mon url et que sa valeur vaut suppression
if(isset($_GET["action"]) && $_GET["action"] == "suppression") {

    // je récupère la valeur du paramètre get id_logement
    $id_logement = $_GET["id_logement"];

    // je supprime en bdd avec cet id
    $count = $pdo->exec("DELETE FROM logement WHERE id_logement = '$id_logement'");


    if($count >= 1 ){
        $content = "<div class='alert alert-success'>
            Votre bien a bien été supprimé !
        </div>";
    }

}

// je récupère tous les logements à la fin
// car si j'en ai supprimé un il faut qu'il soit supprimé en BDD avant que je récupère le tout
$stmt = $pdo->query("SELECT * FROM logement");


require_once("inc/haut_page.php");


?>

<?= $content; ?>

<table border="1">

    <thead>
        <tr>

            <?php for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $infosColumn = $stmt->getColumnMeta($i); ?>
                <th>
                    <?= $infosColumn["name"]; ?>
                </th>
            <?php } ?>

                <th>Fiche logement</th>
                <th>Supprimer</th>

        </tr>
    </thead>

    <tbody>

        <?php

                while ($logement = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?= $logement["id_logement"]; ?></td>
                        <td><?= $logement["titre"]; ?></td>
                        <td><?= $logement["adresse"]; ?></td>
                        <td><?= $logement["ville"]; ?></td>
                        <td><?= $logement["cp"]; ?></td>
                        <td><?= $logement["surface"]; ?></td>
                        <td><?= $logement["prix"]; ?></td>
                        <td>
                            <img style="width:100px" src="<?= $logement["photo"]; ?>" alt="<?= $logement["description"]; ?>">
                        </td>
                        <td><?= $logement["type"]; ?></td>
                        <td><?= $logement["description"]; ?></td>
                        <td><a style="background:black;color:white !important;text-decoration:none;padding:0.5rem 1rem;border-radius:1rem" href="fiche-logement.php?id_logement=<?= $logement["id_logement"]; ?>"> <?= $logement["titre"]; ?> </a> </td>
                        <td><a style="background:red;color:white !important;text-decoration:none;padding:0.5rem 1rem;border-radius:1rem" href="?action=suppression&id_logement=<?= $logement["id_logement"]; ?>"> Supprimer </a> </td>
                    </tr>
                <?php }

        ?>

    </tbody>

</table>



<?php

require_once("inc/bas_page.php");

?>