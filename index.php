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
    <p><a href="signup.php">Nouvelle Partie</a></p>
    <p>Parties Précedentes: </p>
    <?php
    try {
        $connexion = new PDO('mysql:host=localhost;dbname=pendu', 'root', '');
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $requete = $connexion->prepare("Select * from partie");
        $requete->execute();
        echo "<table>
        <tr> 
        <td> Joueur 1 </td>
        <td> Joueur 2 </td>
        <td> Mot </td>
        <td> Numero Vainqueur </td>
        <td> Nombre d'erreurs </td>
        </tr>";
        foreach ($requete as $ligne) {
            echo "<tr> 
                <td> $ligne[nom_joueur1] </td>
                <td> $ligne[nom_joueur2] </td>
                <td> $ligne[mot] </td>
                <td> $ligne[victoire] </td>
                <td> $ligne[nb_coup] </td>
                </tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        die('Erreur PDO : ' . $e->getMessage());
    } catch (Exception $e) {
        die('Erreur générale : ' . $e->getMessage());
    }
    ?>
</body>

</html>