<?php 
  #init-Wert
  $page = 1;

  # group_id per POST empfangen
  $group_id = (int) myPost('urlId', 7);

  # Daten für die Themen-Navigation aus der Datenbank holen,
  # wenn $group_id gesetzt, wird das gewählte Thema gehighlighted
  $groups = getGroups($mysqli, $groupTableName, $group_id);

  # Ermittele die Anzahl der Seiten, die 'durchgeblättert' werden können
  $lastPage = setLastPage($mysqli, $group_id, 1);

  # Pagenummer per POST empfangen
  $page = (int) myPost('page', 2);

  if (myPost('button', 5) === 'check') {
    $case = 3;
  }
  if (myPost('button', 5) === 'reset') {
    $case = 4;
  }
  $page < 0 ? $case = 1 : null;
  $page > $lastPage ? $case = 1 : null;


