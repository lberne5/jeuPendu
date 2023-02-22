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
        if (isset($_POST['motPartieForm']) && !empty($_POST['motPartieForm'])) {
            if (ctype_alpha($_POST['motPartieForm'])) {
                $_SESSION['motADeviner'] = mb_strtoupper($_POST['motPartieForm']);
            } else {
                donneeInvalide();
            }
            $_SESSION['lettresNonPresentes'] = '';
            $_SESSION['nbCoupUtilise'] = 0;
            $_SESSION['motAAfficher'] = str_repeat("-", strlen($_SESSION['motADeviner']));
        }
        if (isset($_POST['lettreProposee']) && !empty($_POST['lettreProposee'])) {
            if (!ctype_alpha($_POST['lettreProposee'])) {
                echo "<script>alert(\" Merci d'entrer des lettres et non des chiffres\");</script>";
            } else {
                if (strlen($_POST['lettreProposee']) > 1) {
                    echo '<p>Merci de ne rentrer qu\'une lettre à la fois.';
                } else {
                    $i = 0;
                    $lettreNonProposee = true;
                    while ($i < strlen($_SESSION['lettresNonPresentes']) && $lettreNonProposee) {
                        if ($_SESSION['lettresNonPresentes'][$i] == strtoupper($_POST['lettreProposee'])) {
                            $lettreNonProposee = false;
                            echo "<p>Vous avez déjà proposé cette lettre.";
                        }
                        $i += 1;
                    }
                    $i = 0;
                    while ($i < strlen($_SESSION['motAAfficher']) && $lettreNonProposee) {
                        if ($_SESSION['motAAfficher'][$i] == mb_strtoupper($_POST['lettreProposee'])) {
                            $lettreNonProposee = false;
                            echo "<p>Vous avez déjà proposé cette lettre.";
                        }
                        $i += 1;
                    }
                    $i = 0;
                    $lettreTrouvee = false;
                    while (($i < strlen($_SESSION['motADeviner'])) && ($lettreNonProposee)) {
                        if ($_SESSION['motADeviner'][$i] == mb_strtoupper($_POST['lettreProposee'])) {
                            $_SESSION['motAAfficher'][$i] = mb_strtoupper($_POST['lettreProposee']);
                            $lettreTrouvee = true;
                        }
                        $i += 1;
                    }
                    if (!$lettreTrouvee && $lettreNonProposee) {
                        $_SESSION['lettresNonPresentes'] .= mb_strtoupper($_POST['lettreProposee']) . " ";
                        $_SESSION['nbCoupsMax'] -= 1;
                        $_SESSION['nbCoupUtilise'] += 1;
                    }
                }
            }
        }

        if (($_SESSION['nbCoupsMax'] >= 0) && ($_SESSION['motAAfficher'] !=  $_SESSION['motADeviner'])) {
            echo "<p> $_SESSION[motAAfficher] </p>";
            echo '
                <form method="post">
                    <label> ' . $_SESSION['joueur2'] . ', proposez une lettre :
                        <input type="text" name="lettreProposee" minlength="1" maxlength = "1" pattern="[a-zA-Z]+" required>
                    </label>
                    <button type="submit">Envoyer</button>
                     </form>
            ';
            echo "<p> Lettres déjà testées: $_SESSION[lettresNonPresentes]</p>";
        }
        if (($_SESSION['nbCoupsMax'] < 0)) {
            $_SESSION['vainqueur'] = $_SESSION['joueur1'];
            $_SESSION['numVainqueur'] = 1;
        }
        if ($_SESSION['motAAfficher'] ==  $_SESSION['motADeviner']) {
            $_SESSION['vainqueur'] = $_SESSION['joueur2'];
            $_SESSION['numVainqueur'] = 2;
        }
        if (isset($_SESSION['vainqueur'])) {
            echo " <p> $_SESSION[vainqueur] vous avez gagné!</p>";
            echo "<p> Le mot était : $_SESSION[motADeviner] </p>";
            echo "<p>Cliquez <a href= \"signup.php\">ici</a> pour redémarrer une partie";
            echo "<p>Cliquez <a href= \"index.php\">ici</a> pour retourner à l'écran d'accueil.";
            try {
                $connexion = new PDO('mysql:host=localhost;dbname=pendu', 'root', '');
                $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $requete = $connexion->prepare("insert into partie (nom_joueur1, nom_joueur2, mot, victoire, nb_coup) values(?, ?, ?, ?, ?)");
                $requete->bindParam(1, $_SESSION['joueur1']);
                $requete->bindParam(2, $_SESSION['joueur2']);
                $requete->bindParam(3, $_SESSION['motADeviner']);
                $requete->bindParam(4, $_SESSION['numVainqueur']);
                $requete->bindParam(5, $_SESSION['nbCoupUtilise']);
                $requete->execute();
            } catch (PDOException $e) {
                die('Erreur PDO : ' . $e->getMessage());
            } catch (Exception $e) {
                die('Erreur générale : ' . $e->getMessage());
            }
            session_destroy();
        }

        ?>
    </body>

    </html>