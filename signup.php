<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta name="viewport" content="width=device-width">
    <title>Pendu</title>
</head>

<body>
    <h1>Hangman </h1>
    <?php
    session_start();
    function donneeInvalide()
    {
        header('Location: signup.php');
        exit(0);
    }

    if (!isset($_POST['nbCoupsMaxForm']) && !isset($_POST['joueur2Form']) && !isset($_POST['joueur1Form'])) {
        echo '<p>Avant de commencer votre partie, merci de renseigner les informations ci-dessous. </p>
                    <form method="post">
                        <label>Nom du Joueur 1 :
                            <input type="text" name="joueur1Form" pattern="[a-zA-Z]+" required>
                        </label>
                        <label>Nom du Joueur 2 :
                            <input type="text" name="joueur2Form" pattern="[a-zA-Z]+" required>
                        </label>
                        <label>Nombre d\'erreurs maximum (laissez à -1 si vous ne voulez pas de limite) :
                            <input type="number" name="nbCoupsMaxForm" min="-1" max="25" value="-1" required>
                        </label>
                        <button type="submit">Envoyer</button>
                    </form>';
    }

    if (isset($_POST['nbCoupsMaxForm']) && !empty($_POST['nbCoupsMaxForm'])) {
        if (is_numeric($_POST['nbCoupsMaxForm'])) {
            if ($_POST['nbCoupsMaxForm'] < 0) {
                $_SESSION['nbCoupsMax'] = 26;
            } else {
                $_SESSION['nbCoupsMax'] = $_POST['nbCoupsMaxForm'];
            }
        } else {
            donneeInvalide();
        }
        if (isset($_POST['joueur2Form']) && !empty($_POST['joueur2Form'])) {
            if (ctype_alpha($_POST['joueur2Form'])) {
                $_SESSION['joueur1'] = $_POST['joueur1Form'];
            } else {
                donneeInvalide();
            }
            if (isset($_POST['joueur1Form']) && !empty($_POST['joueur1Form'])) {
                if (ctype_alpha($_POST['joueur1Form'])) {
                    $_SESSION['joueur1'] = $_POST['joueur1Form'];
                } else {
                    donneeInvalide();
                }
                echo "<p> $_SESSION[joueur1], merci d'entrer votre mot :</p>";
                echo '
                    <form method="post">
                    <label>Mot à deviner :
                        <input type="text" name="motPartieForm" pattern="[a-zA-Z]+" required>
                    </label>
                    <button type="submit">Envoyer</button>
                     </form>
                ';
            }
        }
    }
    
    if (isset($_POST['motPartieForm']) && !empty($_POST['motPartieForm'])) {
        try {
            $connexion = new PDO('mysql:host=localhost;dbname=pendu', 'root', '');
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $requete = "SELECT COUNT(*) as nbMotCorrespondants FROM lexique WHERE ortho = '" . $_POST['motPartieForm'] . "'";
            $resultat = $connexion->query($requete);
            $resultat = $resultat->fetchAll();
            if ($resultat[0]['nbMotCorrespondants'] >= 1) {
                echo '
                        <form method="post" action="game.php">
                            <label>
                                <input type="hidden" name="motPartieForm">
                            </label>
                            <button type="submit">Passer au jeu</button>
                        </form>
                    ';
            } else {
                echo "<script>alert(\" Merci d'entrer un mot valide\");</script>";
            }
        } catch (PDOException $e) {
            die('Erreur PDO : ' . $e->getMessage());
        } catch (Exception $e) {
            die('Erreur Générale : ' . $e->getMessage());
        }
    }
    ?>

</body>

</html>