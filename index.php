<?php

/*************************************************
 * 
 *   0) Récupérer les tâches du fichier de données
 * 
 * **********************************************/
// Étape 1 : lire le fichier JSON dans une chaîne de caractères
$tachesFichier = "data/memo.json";
$tachesJSON = file_get_contents($tachesFichier);

// Étape 2 : transformer la chaîne JSON dans un tableau associatif PHP
$tachesArray = json_decode($tachesJSON, true);

/***********************
 *
 *  1) Ajouter une tâche
 * 
 * ********************/
// Écrire la condition qui permet de supposer que l'utilisateur a soumit le
// formulaire pour l'ajout d'une tâche.

if (isset($_POST["texteTache"])) {
    $texte = $_POST["texteTache"];

    // Créez un identifiant unique de 13 caractères pour cette tâche
    $idTache = uniqid();

    // Créez une chaîne de caractères contenant la date et l'heure courante au format JSON
    $dateHeureTache = gmdate('Y-m-d\TH:i:s.v\Z');

    // Ajoutez au tableau $tachesArray cette nouvelle tâche à l'étiquette correspondant
    // à l'identifiant de la tâche obtenu ci-dessus.
    $tachesArray[$idTache] = [
        "texte" => $texte,
        "accomplie" => false,
        "dateAjout" => $dateHeureTache,
    ];

    // Transfromer le tableau contenant les tâches en chaîne JSON
    $tachesJSON = json_encode($tachesArray);

    // Écrire la chaîne JSON représente les tâches dans le fichier memo.json
    file_put_contents($tachesFichier, $tachesJSON);
}


/*************************************************************
 *
 *  2) Afficher les tâches : Voir ci-dessous dans le code HTML
 * 
 * **********************************************************/


/************************
 * 
 *  3) Filtrer les tâches
 * 
 * *********************/

// Vérifier si l'utilisateur a cliqué un des boutons de filtre :
if (isset($_GET["action"]) && $_GET["action"] == "filtrer") {
    $tachesArray = array_filter($tachesArray, function ($tache) {
        if ($_GET["accomplie"] == 1) {
            return $tache["accomplie"];
        } else {
            return !$tache["accomplie"];
        }
    });
}

/*********************************
 *  
 *  4) Basculer l'état d'une tâche
 * 
 * ******************************/
// Étape 1 : Vérifier si l'utilisateur a cliqué le bouton "coche" à côté d'une tâche;w
if (isset($_GET["action"]) && $_GET["action"] == "basculer" && isset($_GET["id"])) {
    // Étape 2 : dans le tableau $tachesArray aller à la tache spécifiée par le id envoyé par GET
    $tachesArray[$_GET["id"]]["accomplie"] = !$tachesArray[$_GET["id"]]["accomplie"];
    // Étape 3 : NE PAS OUBLIER de sauvegarder votre tableau de nouveau dans le fichier JSON
    $tachesJSON = json_encode($tachesArray);
    file_put_contents($tachesFichier, $tachesJSON);
    header("Refresh:0 url=index.php");
}

// Point boni
if (isset($_GET["action"]) && $_GET["action"] == "enlever" && isset($_GET["id"])) {
    // Étape 2 : dans le tableau $tachesArray aller à la tache spécifiée par le id envoyé par GET
    unset($tachesArray[$_GET["id"]]);
    // Étape 3 : NE PAS OUBLIER de sauvegarder votre tableau de nouveau dans le fichier JSON
    $tachesJSON = json_encode($tachesArray);
    file_put_contents($tachesFichier, $tachesJSON);
    header("Refresh:0 url=index.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>MEMO | Liste de tâches à compléter</title>
    <meta name="description" content="Application Web de gestion de tâches à compléter.">
    <link rel="stylesheet" href="ressources/css/styles.css">
</head>

<body>
    <div class="conteneur">
        <a href="index.php">
            <h1>MEMO</h1>
        </a>
        <form method="post" autocomplete="off">
            <input autofocus class="quoi-faire" type="text" name="texteTache" placeholder="Tâche à accomplir ...">
        </form>
        <div class="filtres">
            <!-- Les liens suivants permettent de filtrer les tâches -->
            <a href="index.php?action=filtrer&accomplie=1">Complétées</a>
            <a href="index.php?action=filtrer&accomplie=0">Non-complétées</a>
            <a href="index.php">Toutes</a>
        </div>
        <ul class="liste-taches">
            <!-- 
            Utilisez les éléments LI suivants comme gabarits pour l'affichage
            des "tâches".
            
            Remarquez la présence de la classe "accomplie" sur l'élément LI pour le montrer 
            biffé (complété) ou non (dépend de la valeur du champ "accomplie" dans le fichier JSON).
            -->

            <?php
            foreach ($tachesArray as $idTache => $infoTache) :
            ?>


                <li class="<?= $infoTache["accomplie"] == true ? "accomplie" : ""; ?>">
                    <span class="coche"><a href="?action=basculer&id=<?= $idTache; ?>" title="Cliquez pour faire basculer l'état de cette tâche."><img src="ressources/images/coche.svg" alt=""></a></span>
                    <span class="texte"><?= $infoTache["texte"]; ?></span>
                    <span class="ajout"><?= $infoTache["dateAjout"]; ?></span>
                    <span class="delete"><a href="?action=enlever&id=<?= $idTache; ?>" title="Cliquez pour enlever cette tâche."><img src="ressources/images/delete.png" alt=""></a></span>
                </li>

            <?php endforeach; ?>

        </ul>
    </div>
</body>

</html>