<?php 

if (empty($_GET) && empty($_POST)) {

  # --- Daten für die Themen-Navigation aus der Datenbank holen
  $groups = getGroups($mysqli, $groupTableName);
  $data[] = [];
}