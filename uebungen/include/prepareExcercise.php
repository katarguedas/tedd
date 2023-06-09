<?php 

if (!empty($_GET) && empty($_POST)) {
  $case = 2;

  $page = 1;

  # neue Seitennummer per GET empfangen?
  (isset($_GET['page']) && $_GET['page'] > 0) ? $page = (int) filter_input(INPUT_GET, 'page') : $page = 1;

  # ist pagenummer kleiner 0: wird formular nicht dargestellt
  $page < 0 ? $case = 1 : $case = 2;

  # Thema-id per GET empfangen
  $group_id = (int) filter_input(INPUT_GET, 'group_id');

  # Hashwert (Prüfsumme) 
  $gethash = filter_input(INPUT_GET, 'hash');

  # check, ob group-id nicht manipuliert wurde
  if (isset($group_id) && hash(MY_ALGO, $group_id . MY_SALT) != $gethash) {
    $group_id = null;
    $case = 1; // damit wird das Formular nicht abgebildet
    $message = 'Bitte wähle ein Thema aus';
  } else {
    # Ermittele die Anzahl der Seiten, die 'durchgeblättert' werden können
    $lastPage = setLastPage($mysqli, $group_id, 1);
  }

}