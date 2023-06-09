<?php

require_once 'include/loadFiles.php';

# ------- Variablen fürs Template -----------

$groupTableName = 'thema';
$pos = 2;

#---------------------------
$username = '';
if (login_check()) {
  $username = $_SESSION['name'];
}

# -------------------------------------------

# Fall 1:
# aller erster Seitenaufruf, noch keine Auswahl eines Themas erfolgt
# => Themen werden aufgelistget => zweite Nav
# -------------------------------------------------------------------

if (empty($_GET) && empty($_POST)) {

  # --- Daten für die Themen-Navigation aus der Datenbank holen
  $groups = getGroups($mysqli, $groupTableName);
  $data[] = [];
}

#--------------------------------------------------------------
# Fall 2:
# Thema gewählt, jetzt werden Daten aus der Datenbank geladen
# => die Übung startet
# -------------------------------------------------------------
# -- Daten per GET empfangen

if (!empty($_GET) && empty($_POST)) {

  include 'include/prepareExcercise.php';

  # Daten für die Themen-Navigation aus der Datenbank holen,
  # wenn $group_id gesetzt, wird das gewählte Thema gehighlighted
  $groups = getGroups($mysqli, $groupTableName, $group_id);


  # Daten für die Übung aus der Datenbank holen
  if (isset($group_id) && ($page <= $lastPage) && ($lastPage > 0)) {
    $data = getDataWithThemeId($mysqli, $group_id, $page);
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
  $currentValues = getUserInput('plural');

  # Hole mit (POST)-Id Daten aus der Datenbank und vergleiche sie mit dem User-Input
  foreach ($currentValues as $item) {
    $dataFromDB = getNomenDataById($mysqli, $item['id']);
    if ($dataFromDB !== null)
      $userResult = checkUserInput($item['userInput'], $dataFromDB['plural']);
    else
      $userResult = false;

    $data[] = [
      'artikel' => $dataFromDB ? $dataFromDB['artikel'] : '',
      'nomen' => $dataFromDB ? $dataFromDB['nomen'] : '',
      'id' => (int) $item['id'],
      'userInput' => $case == 3 ? $item['userInput'] : '',
      'result' => $case == 3 ? $userResult : ''
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

#----------- T W I G ------------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('/singular-plural.html.twig', [
  'username' => $username,
  'case' => $case,
  'title' => 'Plural',
  'inc' => $incPath,
  'pos' => $pos,
  'themen' => $groups,
  'formAction' => $formAction,
  'data' => $data,
  'count' => count($data),
  'message' => $message,
  'page' => $page,
  'lastPage' => $lastPage
]);