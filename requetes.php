<?php
$baseURL = "http://localhost:8080/rest";
$user = "admin";
$pass = "admin";
$resultat = "";
$query = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $_POST['query'];

    // Envelopper la requête dans une balise racine
    $wrappedQuery = 'let $r := (' . $query . ') return <resultats>{$r}</resultats>';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$baseURL?query=" . urlencode($wrappedQuery));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
    $resultat = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        $resultat = "❌ Erreur : $resultat";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Requêtes — Club Info_Tech</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <h1>🏆 Club Info_Tech</h1>
  <nav>
    <a href="index.php">Concours</a>
    <a href="resultats.php">Résultats</a>
    <a href="inscription.php">Inscription</a>
    <a href="requetes.php">Requêtes</a>
  </nav>
</header>

<main>
  <h2>🔍 Requêtes XQuery Libres</h2>

  <form method="POST">
    <label>Entrez votre requête XQuery :</label>
    <textarea name="query" rows="8" 
      placeholder="ex: collection('club')//membre"><?= htmlspecialchars($query) ?></textarea>
    <button type="submit">Exécuter</button>
  </form>

  <?php if ($resultat): ?>
  <br>
  <h3>Résultat :</h3>
  <pre><?= htmlspecialchars($resultat) ?></pre>
  <?php endif; ?>

</main>

</body>
</html>