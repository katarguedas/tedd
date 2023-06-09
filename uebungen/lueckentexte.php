<?php

require_once 'include/loadFiles.php';

# ------- Variablen fürs Template -----------

$declinations = [
  'der' => ['--', 'der', 'des', 'den', 'dem'],
  'das' => ['--', 'das', 'des', 'dem'],
  'die' => ['--', 'die', 'der'],
  'ohne' => ['--', 'der', 'das', 'den', 'die'],
  'plural' => '--',
  ['die', 'der', 'den'],
  'ein' => ['--', 'ein', 'eines', 'einem', 'einen'],
  'eine' => ['--', 'eine', 'einer']
];

$groupTableName = 'kategorien';
$pos = 3;

# -----------------------------

$username = '';
if (login_check()) {
  $username = $_SESSION['name'];
}

# -------------------------------------------

# Fall 1:
# aller erster Seitenaufruf, noch keine Auswahl einer Kategorie erfolgt
# => Kategorien werden aufgelistget => zweite Nav
# -------------------------------------------------------------------

if (empty($_GET) && empty($_POST)) {

  # --- Daten für die Kategorien aus der Datenbank holen
  $groups = getGroups($mysqli, $groupTableName);
  $data[] = [];
}

#--------------------------------------------------------------
# Fall 2:
# Kategorie(group) gewählt, jetzt werden Daten aus der Datenbank geladen
# => die Übung startet
# -------------------------------------------------------------
# -- Daten per GET empfangen

if (!empty($_GET) && empty($_POST)) {

  include 'include/prepareExcercise.php';

  # Daten für die Kategorien aus der Datenbank holen,
  # wenn $group_id gesetzt, wird die gewählte group gehighlighted
  $groups = getGroups($mysqli, $groupTableName, $group_id);

  # Daten für die Übung aus der Datenbank holen
  if (isset($group_id) && ($page <= $lastPage) && ($lastPage > 0)) {
    $data = getDataWithCategoryId($mysqli, $group_id, $page);
  } else {
    $data[] = [];
    $case = 1;
  }
}

# -------------------------------------------------------------
# Fall 3 :
# 'prüfen' Button geklickt: 
# - selection des Users wird über POST übertragen, 
# - im hidden input kommen die ids der aktuellen Übung,
# => Auswertung der Daten und Darstellung des Ergebnisses für den User
# oder Fall 4:
# 'reset' Button geklickt:
# - die abgebildeten Übungen sollen beibehalten werden,
# - die User-Eingabe wird 'gelöscht' => gleiche Übung ohne Auswahl
# -------------------------------------------------------------

if (!empty($_POST)) {

  require_once 'include/handleExcercise.php';

    # Prüfe die User-Eingabe für Artikel und hole sie sowie die aktuellen Artikel-Ids aus $_POST:
    $currentValues = getUserInput('artikel');
    
  # Hole mit (POST)-Id Daten aus der Datenbank und vergleiche sie mit dem User-Input
  foreach ($currentValues as $item) {
    $dataFromDB = getTextDataById($mysqli, $item['id']);
    if ($dataFromDB !== null)
      $userResult = checkUserInput($item['userInput'], $dataFromDB['loesung']);
    else
      $userResult = false;

    $data[] = [
      'textPart1' => $dataFromDB ? $dataFromDB['textteil1'] : '',
      'textPart2' => $dataFromDB ? $dataFromDB['textteil2'] : '',
      'textPart3' => $dataFromDB ? $dataFromDB['textteil3'] : '',
      'id' => (int) $item['id'],
      'userInput' => $case == 3 ? $item['userInput'] : null,
      'result' => $case == 3 ? $userResult : null
    ];
  }
}
# -------------------------------------------------------------
# Fall x:
# keiner der vier oben genannten fälle trifft zu
# => es wird nur die linke Navigation der Themen/Kategorien dargestellt

require_once 'include/caseZero.php';

# -------------------------------------------


# --- Datenbankverbindung schließen --------

mysqli_close($mysqli);


#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('/lueckentexte.html.twig', [
  'username' => $username,
  'title' => 'Lückentexte',
  'inc' => $incPath,
  'pos' => $pos,
  'categories' => $groups,
  'data' => $data,
  'declinations' => $declinations,
  'case' => $case,
  'message' => $message,
  'page' => $page,
  'lastPage' => $lastPage
]);