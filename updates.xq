(: ================================================
   FICHIER : updates.xq
   PROJET  : Club Info_Tech
   NOTE    : Exécuter chaque update séparément
             dans BaseX GUI
   ================================================ :)

(: ────────────────────────────────────────────────
   UPDATE 1 - Insertion d'un nouveau membre
   ──────────────────────────────────────────────── :)

(: Insérer un nouveau nœud membre dans la liste des membres :)
insert node
  (: Définir le nouveau membre avec ses attributs et éléments :)
  <membre id="M010" categorieRef="C2">
    (: Nom du nouveau membre :)
    <nom>Zerrouk</nom>
    (: Prénom du nouveau membre :)
    <prenom>Lyna</prenom>
    (: Email du nouveau membre :)
    <email>l.zerrouk@club.dz</email>
  </membre>
(: Cibler l'élément membres de la base de données club :)
into collection("club")//membres

(: ────────────────────────────────────────────────
   UPDATE 2 - Modification du coefficient de CO2
   Avant : coefficient="1.2"  →  Après : "2.0"
   ──────────────────────────────────────────────── :)

(: Remplacer la valeur de l'attribut coefficient du concours CO2 :)
replace value of node
  (: Cibler l'attribut coefficient du concours ayant l'id CO2 :)
  collection("club")//concours[@id="CO2"]/@coefficient
(: Nouvelle valeur du coefficient :)
with "2.0"

(: ────────────────────────────────────────────────
   UPDATE 3 - Suppression d'un participant de CO1
   Supprime le participant M003 du concours CO1
   ──────────────────────────────────────────────── :)

(: Supprimer le nœud participant M003 du concours CO1 :)
delete node
  (: Cibler le participant M003 dans les participants du concours CO1 :)
  collection("club")//concours[@id="CO1"]//participant[@membreRef="M003"]