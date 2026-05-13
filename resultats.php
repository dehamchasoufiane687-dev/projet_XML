<?php
$baseURL = "http://localhost:8080/rest";
$user = "admin";
$pass = "admin";

// Récupérer la liste des concours
$queryConcours = urlencode('
for $c in collection("club")//concours[@id]
return <concours>
  <id>{$c/@id/string()}</id>
  <titre>{$c/titre/text()}</titre>
</concours>
');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL?query=$queryConcours");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
$resConcours = curl_exec($ch);
curl_close($ch);
$xmlConcours = simplexml_load_string("<root>$resConcours</root>");

// Résultats du concours sélectionné
$resultats = null;
$selectedId = "";

if (isset($_GET['concoursId']) && $_GET['concoursId'] != "") {
    $selectedId = $_GET['concoursId'];

    $queryResultats = urlencode("
    let \$c := collection(\"club\")//concours[@id=\"$selectedId\"]
    let \$coeff := xs:decimal(\$c/@coefficient)
    let \$scores :=
      for \$p in \$c//participant
      return (xs:decimal(\$p/complexite) + xs:decimal(\$p/tempsExecution)) * \$coeff
    let \$maxScore := max(\$scores)
    for \$p in \$c//participant
    let \$m := collection(\"club\")//membre[@id = \$p/@membreRef]
    let \$score := (xs:decimal(\$p/complexite) + xs:decimal(\$p/tempsExecution)) * \$coeff
    order by \$score descending
    return <resultat>
      <nom>{\$m/prenom/text()} {\$m/nom/text()}</nom>
      <complexite>{\$p/complexite/text()}</complexite>
      <temps>{\$p/tempsExecution/text()}</temps>
      <score>{format-number(\$score, '0.00')}</score>
      <vainqueur>{\$score = \$maxScore}</vainqueur>
    </resultat>
    ");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$baseURL?query=$queryResultats");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
    $resResultats = curl_exec($ch);
    curl_close($ch);
    $resultats = simplexml_load_string("<root>$resResultats</root>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultats — Club Info_Tech</title>
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
  <h2>🏅 Résultats par Concours</h2>

  <form method="GET">
    <label>Sélectionner un concours :</label>
    <select name="concoursId">
      <option value="">-- Choisir --</option>
      <?php foreach ($xmlConcours->concours as $c): ?>
        <option value="<?= $c->id ?>" <?= $c->id == $selectedId ? 'selected' : '' ?>>
          <?= $c->id ?> — <?= $c->titre ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit"