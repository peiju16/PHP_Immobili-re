<?php

########################################
#||                                  #||
#||                                  #||
#||                                  #||
#||           MODIFICATION           #||
#||                                  #||
#||                                  #||
#||                                  #||
########################################

// récupérer la connexion à la bdd
// et accès à URL
// et RACINE_SITE pour la copie de l'image
require_once("inc/config.php");

// AJOUTER UN LIEN À LA CARD POUR ETRE REDIRIGIÉ VERS INDEX.PHP
// AVEC UN PARAMÈTRE ID LOGEMENT ET L'ID DU LOGEMENT
// PARAMETRE ACTION = MODIFICATION ?id_logement=2&action=modification
// ARRIVÉ ICI VOUS ALLEZ DIRE SI J'AI ACTION = MODIFICAITON
// JE RECUPERER L'ID LOGEMENT
// JE METS L'ID LOGEMENT DANS UN INPUT TYPE HIDDEN DANS MON FORMULAIRE
// CAR J'EN AURAIS BESOIN AU MOMENT DE LA SOUMISSSION DU FORMULAIRE POUR UPDATE SES DONNNÉES
// si PARAMETRE ACTION = MODIFICATION alors le bouton de mon formulaire ce sera un bouton de modificaiton
// le soucis qu'on va avoir c'est du coup ma page elle pourra faire deux posts diffférents
// un ce sera un ajout
// un ce sera une modification
// A PARTIR L'ID LOGEMENT VOUS RECUPEREREZ TOUTES LES INFOS EN BDD
// ET VOUS PRECHARGEREZ LES CHAMPS AVEC

$loadingForModification = false;

// variabiliser le contenu
// fais le controler de nos champs
// controller le fichier uploader
// insert
// variabiliser les données du form en cas d'erreur pour les réafficher à l'écran


// variabilise le contenu a afficher sur la page (contenu ou des erreurs)
$content = "";
$form_valid = true;

if ($_POST) { // si je soumets mon formulaire en POST

    // controller format code postal
    // strlen() fonction predefinie qui me renvoit le nombre de caracteres
    if (isset($_POST["cp"]) && (strlen($_POST["cp"]) < 5 || strlen($_POST["cp"])) > 5) {
        $content .= "<div class='alert alert-danger'>
            Veuillez renseigner un code postal valide !
        </div>";
    }

    // surface et prix entier
    // is_numeric permet de savoir si dans une string j'ai un chiffre
    if (isset($_POST["surface"]) && !is_numeric($_POST["surface"])) {
        $content .= "<div class='alert alert-danger'>
        Veuillez renseigner une surface avec un chiffre entier
    </div>";
    }

    if (isset($_POST["prix"]) && !is_numeric($_POST["prix"])) {
        $content .= "<div class='alert alert-danger'>
        Veuillez renseigner un prix avec un chiffre entier
    </div>";
    }

    // vérifier les champs et afficher des erreurs si erreur il y a
    // si j'ai un nom pour le fichier chargé c'est que j'ai chargé quelque chose
    // j'ai de verifier ca sinon $_FILES["photo"]["size"] me renverra une erreur si dans $_FILES["photo"] j'ai rien
    if (!empty($_FILES["photo"]["name"])) {
        $has_picture = true;
        // photo extension et poids

        $maxSize = 1000000; // 1MO

        // si ma photo elle fait plus de 1M0 erreur
        if ($_FILES["photo"]["size"] > $maxSize) {
            $content .= "<div class='alert alert-primary' role='alert'>
            Veuillez insérer un fichier inférieur à 1MO !
        </div>";
        }

        $extensions = ["image/png", "image/jpg", "image/jpeg"];
        // si l'extension de ma photo est pas dans mon tableau d'extensions erreurs
        if (!in_array($_FILES["photo"]["type"], $extensions)) {
            $content .= "<div class='alert alert-primary' role='alert'>
                Veuillez insérer une image au format jpg, jpeg ou png !
            </div>";
        }

        if (empty($content)) { // si je n'ai pas d'erreur

            // controller l'extension de l'image
            // controller sa taille

            // nom pour la photo
            $extension = strchr($_FILES["photo"]["name"], ".");
            $pictureName = "logement_" . time() . $extension;

            // copie le chemin de la photo sur le serveur en BDD
            // http://localhost:8080/PHP_APFA_2024/img/NOM_IMAGE.extension
            $pathPictureForDB = URL . "img/" . $pictureName;

            // dossier où copier l'image sur le serveur
            // /Applications/MAMP/htdocs/PHP_APFA_2024/img/NOM_IMAGE.extension
            $pathFolder = RACINE_SITE . "img/" . $pictureName;

            // copier l'image sur le serveur
            copy($_FILES["photo"]["tmp_name"], $pathFolder);
        }


    }

    // si a ce stade $content est vide
    // c'est que j'ai pas d'erreur => insert
    if (empty($content)) {

        extract($_POST); // elle va créer une variable pour chaque index de mon tableau en mettant sa valeur à l'intérieur
        // chaque variable aura le nom d'un index du tableau

        if($_POST["modification"]) {

            $update = "UPDATE logement 
            SET titre = '$titre', 
            adresse = '$adresse', 
            surface = '$surface', 
            prix = '$prix', 
            description = '$description', 
            ville = '$ville', 
            cp = '$cp'";

            if(isset($has_picture) && $has_picture) {
                $update .= ", photo = '$pathPictureForDB'";
            }

            $update .= " WHERE id_logement = '$id_logement'";


            $count = $pdo->exec($update);

            if($count >= 1) {
                $content .= "<div class='alert alert-success' role='alert'>
                    Votre bien a bien été modifié en BDD !
                </div>";
            }



        } else {

            $count = $pdo->exec("
                INSERT INTO logement (titre, adresse, surface, prix, description, photo, ville, cp)
                VALUES(
                    '$titre',
                    '$adresse',
                    '$surface',
                    '$prix',
                    '$description',
                    '$pathPictureForDB',
                    '$ville',
                    '$cp'
                )");
    
            // si j'ai au moins une ligne d'insérée après mon exec
            // msg de confirmation
            if ($count >= 1) {
                // afficher un msg de confirmation si l'insert s'est bien fait
                $content .= "<div class='alert alert-success' role='alert'>
                    Votre bien a bien été ajouté en BDD !
                </div>";
            }

        }






    } else { // ca veut dire que j'ai des erreurs
        $form_valid = false;

    }

}

if (isset($_GET["action"]) && $_GET["action"] == "modification") {

    // charger les données du logement que je veux afficher en bdd
    $loadingForModification = true;
    $id_logement = $_GET["id_logement"];
    $stmt = $pdo->query("SELECT * FROM logement WHERE id_logement = '$id_logement'");
    $logement = $stmt->fetch(PDO::FETCH_ASSOC);

}


// permet de ne pas perdre mes saisies en cas d'erreur après le POST
// variabiliser les données du form en cas d'erreur pour les réafficher à l'écran

// le cas ou j'ajoute ou modifie (post) et j'ai des erreurs
if (!$form_valid) {
    $titre = isset($_POST["titre"]) ? $_POST["titre"] : "";
    $surface = isset($_POST["surface"]) ? $_POST["surface"] : "";
    $prix = isset($_POST["prix"]) ? $_POST["prix"] : "";
    $type = isset($_POST["type"]) ? $_POST["type"] : "";
    $adresse = isset($_POST["adresse"]) ? $_POST["adresse"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $ville = isset($_POST["ville"]) ? $_POST["ville"] : "";
    $cp = isset($_POST["cp"]) ? $_POST["cp"] : "";
} else if ($loadingForModification) { // le cas ou je veux pré charger mes données dans le formulaire
    $id_logement = $logement["id_logement"];
    $titre = $logement["titre"];
    $surface = $logement["surface"];
    $prix = $logement["prix"];
    $type = $logement["type"];
    $adresse = $logement["adresse"];
    $description = $logement["description"];
    $ville = $logement["ville"];
    $cp = $logement["cp"];
    $photo = $logement["photo"];
}


require_once("inc/haut_page.php");

?>

<?= $content; ?>

<div class="container">
    <a href="liste-logements.php">Tableau</a>
    <form method="post" enctype="multipart/form-data">
        <?php if($loadingForModification) {?>
            <input type="hidden" name="id_logement" value="<?= isset($id_logement) ? $id_logement : ""; ?>">
        <?php } ?>
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input required class="form-control" type="text" aria-label=".form-control-lg example"
                value="<?= isset($titre) ? $titre : ""; ?>" name="titre">
        </div>
        <div class="mb-3">
            <label class="form-label">Adresse</label>
            <input required class="form-control " type="text" aria-label=".form-control-lg example"
                value="<?= isset($adresse) ? $adresse : ""; ?>" name="adresse">
        </div>
        <div class="mb-3">
            <label class="form-label">Ville</label>
            <input required class="form-control " type="text" aria-label=".form-control-lg example"
                value="<?= isset($ville) ? $ville : ""; ?>" name="ville">
        </div>
        <div class="mb-3">
            <label class="form-label">Code Postale</label>
            <input required class="form-control" type="text" aria-label=".form-control-lg example"
                value="<?= isset($cp) ? $cp : ""; ?>" name="cp">
        </div>
        <div class="mb-3">
            <label class="form-label">Surface</label>
            <input required class="form-control " type="number" aria-label=".form-control-lg example"
                value="<?= isset($surface) ? $surface : ""; ?>" name="surface">
        </div>
        <div class="mb-3">
            <label class="form-label">Prix</label>
            <input required class="form-control " type="number" aria-label=".form-control-lg example"
                value="<?= isset($prix) ? $prix : ""; ?>" name="prix">
        </div>
        <div class="mb-3">
            <label for="floatingTextarea">Description</label>
            <textarea name="description" class="form-control" placeholder="Leave a comment here" id="floatingTextarea">
                <?= isset($description) ? $description : ""; ?>
            </textarea>
        </div>
        <div class="mb-3">
            <label for="formFile" class="form-label">put your image</label>
            <input name="photo" class="form-control" accept="image/png, image/jpeg" type="file" id="formFile">
            <?php if ($loadingForModification) { ?>
                <img style="width:100px" src="<?= isset($photo) ? $photo : ""; ?>"
                    alt="<?= isset($description) ? $description : ""; ?>">
            <?php } ?>

        </div>
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select value="<?= isset($type) ? $type : ""; ?>" name="type" class="form-select"
                aria-label="Default select example">
                <?php if(isset($type) && $type == "location") { ?>
                    <option selected value="location">location</option>
                    <option value="vente">vente</option>
                <?php } else if(isset($type) && $type == "vente") { ?>
                    <option value="location">location</option>
                    <option selected value="vente">vente</option>
                <?php } else { ?>
                    <option value="location">location</option>
                    <option value="vente">vente</option>
                <?php } ?>
            </select>
        </div>

        <?php if ($loadingForModification) { ?>
            <!-- LE BOUTON MODIFICAITON FAUDRA L'AFFICHER QUE DANS LE CADRE D'UNE MODIFICATION -->
            <input type="submit" name="modification" class="btn btn-primary" value="Modifier">
        <?php } else { ?>
            <input type="submit" name="ajout" class="btn btn-primary" value="Ajouter">
        <?php } ?>

    </form>


    <?php

    require_once("inc/bas_page.php");

    ?>