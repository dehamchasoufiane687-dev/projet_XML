<?php
// Connexion à BaseX REST API
$baseURL = "http://localhost:8080/rest";
$user = "admin";
$pass = "admin";

// Requête XQuery pour liste des concours
$query = urlencode('
for $c in collection("club")//concours[@id]
let $cat := collection("club")//categorie[@id = $c/@categorieRef]
order by $c/@date
return <concours>
  <id>{$c/@id/string()}</id>
  <titre>{$c/titre/text()}</titre>
  <date>{$c/@date/string()}</date>
  <coefficient>{$c/@coefficient/string()}</coefficient>
  <categorie>{$cat/@libelle/string()}</categorie>
</concours>
');

// Appel REST
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL?query=$query");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
$result = curl_exec($ch);
curl_close($ch);

// Parser le résultat XML
$xml = simplexml_load_string("<root>$result</root>");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Club Info_Tech</title>
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
  <h2>📋 Liste des Concours</h2>
  <table>
    <thead>
      <tr>
        <th>Titre</th>
        <th>Date</th>
        <th>Catégorie</th>
        <th>Coefficient</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($xml->concours as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c->titre) ?></td>
        <td><?= htmlspecialchars($c->date) ?></td>
        <td><?= htmlspecialchars($c->categorie) ?></td>
        <td><?= htmlspecialchars($c->coefficient) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

</body>
</html>