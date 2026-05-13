(: ================================================
   FICHIER : requetes.xq
   PROJET  : Club Info_Tech
   NOTE    : Exécuter chaque requête séparément
             dans BaseX GUI
   ================================================ :)

(: ────────────────────────────────────────────────
   Q1 - Liste complète des membres
   ──────────────────────────────────────────────── :)

(: Charger le document XML club.xml :)
let $db := doc("C:/Users/Dynabook/Desktop/mini_projetXml/club.xml")
return
<membres>
{
  (: FOR : parcourir tous les membres du document :)
  for $m in $db//membre

  (: LET : récupérer la catégorie liée via categorieRef :)
  let $cat := $db//categorie[@id = $m/@categorieRef]

  (: RETURN : retourner chaque membre avec ses informations :)
  return
    <membre id="{$m/@id}">
      <nomComplet>{$m/prenom/text()," ",$m/nom/text()}</nomComplet>
      <email>{$m/email/text()}</email>
      <categorie>{$cat/@libelle/string()}</categorie>
    </membre>
}
</membres>

(: ────────────────────────────────────────────────
   Q2 - Liste des concours triés par date croissante
   ──────────────────────────────────────────────── :)

(: Charger le document XML club.xml :)
let $db := doc("C:/Users/Dynabook/Desktop/mini_projetXml/club.xml")
return
<concours>
{
  (: FOR : parcourir tous les concours ayant un attribut id :)
  for $c in $db//concours[@id]

  (: LET : récupérer le libellé de la catégorie du concours :)
  let $cat := $db//categorie[@id = $c/@categorieRef]

  (: ORDER BY : trier les concours par date croissante :)
  order by $c/@date

  (: RETURN : retourner chaque concours avec ses informations :)
  return
    <concours>
      <titre>{$c/titre/text()}</titre>
      <date>{$c/@date/string()}</date>
      <coefficient>{$c/@coefficient/string()}</coefficient>
      <categorie>{$cat/@libelle/string()}</categorie>
    </concours>
}
</concours>

(: ────────────────────────────────────────────────
   Q3 - Calcul du score de chaque participant
   Formule : score = (complexite + tempsExecution) x coefficient
   ──────────────────────────────────────────────── :)

(: Charger le document XML club.xml :)
let $db := doc("C:/Users/Dynabook/Desktop/mini_projetXml/club.xml")
return
<resultats>
{
  (: FOR : parcourir tous les concours ayant un attribut id :)
  for $c in $db//concours[@id]

  (: FOR : parcourir tous les participants de chaque concours :)
  for $p in $c//participant

  (: LET : récupérer les informations du membre participant :)
  let $membre := $db//membre[@id = $p/@membreRef]

  (: LET : convertir le coefficient en décimal :)
  let $coeff  := xs:decimal($c/@coefficient)

  (: LET : convertir la complexité en décimal :)
  let $compl  := xs:decimal($p/complexite)

  (: LET : convertir le temps d'exécution en décimal :)
  let $temps  := xs:decimal($p/tempsExecution)

  (: LET : calculer le score final du participant :)
  let $score  := ($compl + $temps) * $coeff

  (: RETURN : retourner le résultat de chaque participant :)
  return
    <resultat>
      <concours>{$c/titre/text()}</concours>
      <participant>{$membre/prenom/text()," ",$membre/nom/text()}</participant>
      <complexite>{$p/complexite/text()}</complexite>
      <tempsExecution>{$p/tempsExecution/text()}</tempsExecution>
      <score>{format-number($score, "0.00")}</score>
    </resultat>
}
</resultats>

(: ────────────────────────────────────────────────
   Q4 - Vainqueur de chaque concours
   ──────────────────────────────────────────────── :)

(: Charger le document XML club.xml :)
let $db := doc("C:/Users/Dynabook/Desktop/mini_projetXml/club.xml")
return
<vainqueurs>
{
  (: FOR : parcourir tous les concours ayant un attribut id :)
  for $c in $db//concours[@id]

  (: LET : convertir le coefficient en décimal :)
  let $coeff := xs:decimal($c/@coefficient)

  (: LET : calculer le score maximum parmi tous les participants :)
  let $maxScore := max(
    for $p in $c//participant
    return (xs:decimal($p/complexite) + xs:decimal($p/tempsExecution)) * $coeff
  )

  (: FOR : parcourir à nouveau les participants pour trouver le vainqueur :)
  for $p in $c//participant

  (: LET : calculer le score de chaque participant :)
  let $score := (xs:decimal($p/complexite) + xs:decimal($p/tempsExecution)) * $coeff

  (: WHERE : garder uniquement le participant avec le score maximum :)
  where $score = $maxScore

  (: LET : récupérer les informations du membre vainqueur :)
  let $m := $db//membre[@id = $p/@membreRef]

  (: RETURN : retourner le vainqueur avec son score :)
  return
    <vainqueur>
      <concours>{$c/titre/text()}</concours>
      <nom>{$m/nom/text()}</nom>
      <prenom>{$m/prenom/text()}</prenom>
      <score>{format-number($maxScore, "0.00")}</score>
    </vainqueur>
}
</vainqueurs>

(: ────────────────────────────────────────────────
   Q5 - Membres d'une catégorie triés alphabétiquement
   ──────────────────────────────────────────────── :)

(: Charger le document XML club.xml :)
let $db := doc("C:/Users/Dynabook/Desktop/mini_projetXml/club.xml")

(: LET : définir la catégorie à filtrer :)
let $categorie := "Intelligence Artificielle"
return
<membres>
{
  (: LET : récupérer l'ID de la catégorie demandée :)
  let $catId := $db//categorie[@libelle = $categorie]/@id

  (: FOR : parcourir les membres appartenant à cette catégorie :)
  for $m in $db//membre[@categorieRef = $catId]

  (: ORDER BY : trier alphabétiquement par nom puis prénom :)
  order by $m/nom, $m/prenom

  (: RETURN : retourner chaque membre trié :)
  return
    <membre>
      <nom>{$m/nom/text()}</nom>
      <prenom>{$m/prenom/text()}</prenom>
      <email>{$m/email/text()}</email>
    </membre>
}
</membres>