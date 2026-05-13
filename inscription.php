<?php
$baseURL = "http://localhost:8080/rest";
$user = "admin";
$pass = "admin";
$message = "";

// Récupérer la liste des membres
$queryMembres = urlencode('
for $m in collection("club")//membre
return <membre>
  <id>{$m/@id/string()}</id>
  <nom>{$m/prenom/text()} {$m/nom/text()}</nom>
</membre>
');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseURL?query=$queryMembres");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
$resMembres = curl_exec($ch);
curl_close($ch);
$xmlMembres = simplexml_load_string("<root>$resMembres</root>");

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

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $membreRef  = $_POST['membreRef'];
    $concoursId = $_POST['concoursId'];
    $complexite = $_POST['complexite'];
    $temps      = $_POST['tempsExecution'];

    $queryInsert = urlencode("
    insert node
      <participant membreRef=\"$membreRef\">
        <complexite>$complexite</complexite>
        <tempsExecution>$temps</tempsExecution>
      </participant>
    into collection(\"club\")//concours[@id=\"$concoursId\"]//participants
    ");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$baseURL?query=$queryInsert");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
    curl_setopt($ch, CURLOPT_POST, true);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $message = "<p class='success'>✅ Inscription réussie !</p>";
    } else {
        $message = "<p class='error'>❌ Erreur : $res</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription — Club Info_Tech</title>
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
  <h2>📝 Inscription à un Concours</h2>
  <?= $message ?>
  <form method="POST">
    <label>Membre :</label>
    <select name="membreRef">
      <?php foreach ($xmlMembres->membre as $m): ?>
        <option value="<?= $m->id ?>"><?= $m->id ?> — <?= $m->nom ?></option>
      <?php endforeach; ?>
    </select>

    <label>Concours :</label>
    <select name="concoursId">
      <?php foreach ($xmlConcours->concours as $c): ?>
        <option value="<?= $c->id ?>"><?= $c->id ?> — <?= $c->titre ?></option>
      <?php endforeach; ?>
    </select>

    <label>Complexité (0-100) :</label>
    <input type="number" name="complexite" min="0" max="100" required>

    <label>Temps d'exécution (ms) :</label>
    <input type="number" name="tempsExecution" min="1" required>

    <button type="submit">S'inscrire</button>
  </form>
</main>

</body>
</html>