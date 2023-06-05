<?php

require_once '../config.php';
require_once '../helpers/functions.php';

# ---- Datenbankverbindung ------------------

require_once '../helpers/db-connection.php';

# -------------------------------------------

$headerPath = './components/header.html.twig';
$footerPath = './components/footer.html.twig';


# ------- Variablen fürs Template -----------

$formAction = $_SERVER['SCRIPT_NAME'];
$options = ['--', 'der', 'die', 'das'];
$case = 0;
$message = '';
$page = 0;
$lastPage = 0;

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

  $case = 1;

  # --- Daten für die Themen-Navigation aus der Datenbank holen
  $themen = getThemes($mysqli);
  $data[] = [];
}

#--------------------------------------------------------------

# Fall 2:
# Thema gewählt, jetzt werden Daten aus der Datenbank geladen
# => die Übung startet
# -------------------------------------------------------------
# -- Daten per GET empfangen

if (!empty($_GET) && empty($_POST)) {

  $case = 2;

  $page = 1;

  # neue Seitennummer per GET empfangen?
  (isset($_GET['page']) && $_GET['page'] > 0) ? $page = (int) filter_input(INPUT_GET, 'page') : $page = 1;

  # ist pagenummer kleiner 0: wird formular nicht dargestellt
  $page < 0 ? $case = 1 : $case = 2;

  # Thema-id per GET empfangen
  $thema_id = (int) filter_input(INPUT_GET, 'thema_id');

  # Hashwert (Prüfsumme) 
  $gethash = filter_input(INPUT_GET, 'hash');

  # check, ob thema-id nicht manipuliert wurde
  if (isset($thema_id) && hash(MY_ALGO, $thema_id . MY_SALT) != $gethash) {
    $thema_id = null;
    $case = 1; // damit wird das Formular nicht abgebildet
    $message = 'Bitte wähle ein Thema aus';
  } else {
    # Ermittele die Anzahl der Seiten, die 'durchgeblättert' werden können
    $lastPage = setLastPage($mysqli, $thema_id);
  }

  # Daten für die Themen-Navigation aus der Datenbank holen,
  # wenn $thema_id gesetzt, wird das gewählte Thema gehighlighted
  $themen = getThemes($mysqli, $thema_id);

  # Daten für die Übung aus der Datenbank holen
  if (isset($thema_id) && ($page <= $lastPage) && ($lastPage > 0)) {
    $data = getDataWithThemeId($mysqli, $thema_id, $page);
  } else {
    $data[] = [];
    $case = 1;
  }
}

# -------------------------------------------------------------
# Fall 3:
# 'prüfen' Button geklickt: 
# - selection des Users wird über POST übertragen, 
# - im hidden input kommen die ids der aktuellen Übung,
# => Auswertung der Daten und Darstellung des Ergebnisses für den User
# -------------------------------------------------------------

// if (empty($_GET) && !empty($_POST) && myPost('button', 5) === 'check') {
if (!empty($_POST) && myPost('button', 5) === 'check') {

  $case = 3;

  $page = 1;

  # Thema_id per POST empfangen
  $thema_id = (int) myPost('urlId', 7);
  # Daten für die Themen-Navigation aus der Datenbank holen,
  # wenn $thema_id gesetzt, wird das gewählte Thema gehighlighted
  $themen = getThemes($mysqli, $thema_id);

  # Ermittele die Anzahl der Seiten, die 'durchgeblättert' werden können
  $lastPage = setLastPage($mysqli, $thema_id);

  # Pagenummer per POST empfangen
  $page = (int) myPost('page', 2);
  $page < 0 ? $case = 1 : $case = 3;
  $page > $lastPage ? $case = 1 : $case = 3;

  # Prüfe die User-Eingabe für Artikel und hole sie sowie die aktuellen Artikel-Ids aus $_POST:
  $currentValues = getUserInput();

  # Hole mit (POST)-Id Daten aus der Datenbank und vergleiche sie mit dem User-Input
  foreach ($currentValues as $item) {
    $dataFromDB = getNomenDataById($mysqli, $item['id']);
    if ($dataFromDB !== null)  //Daten nicht manipuliert
      $userResult = checkUserInput($item['userInput'], $dataFromDB['artikel']);
    else
      $userResult = false;

    $data[] = [
      'artikel' => $dataFromDB ? $dataFromDB['artikel'] : '' ,
      'nomen' => $dataFromDB ? $dataFromDB['nomen'] : '',
      'id' => (int) $item['id'],
      'userInput' => $item['userInput'],
      'result' => $userResult
    ];
  }
}

# -------------------------------------------------------------
# Fall 4:
# 'reset' Button geklickt:
# - die abgebildeten Übungen sollen beibehalten werden,
# - die User-Eingabe wird 'gelöscht' => gleiche Übung ohne Auswahl
# -------------------------------------------------------------

if (empty($_GET) && !empty($_POST) && myPost('button', 5) === 'reset') {

  $case = 4;

  $page = 1;

  $thema_id = (int) myPost('urlId', 7);
  $themen = getThemes($mysqli, $thema_id);

  # Ermittele die Anzahl der Seiten, die 'durchgeblättert' werden können
  $lastPage = setLastPage($mysqli, $thema_id);

  # Pagenummer per POST empfangen
  $page = (int) myPost('page', 2);
  $page < 0 ? $case = 1 : $case = 4;
  $page > $lastPage ? $case = 1 : $case = 4;


  # Prüfe die User-Eingabe für Artikel und hole sie sowie die aktuellen Artikel-Ids aus $_POST:
  $currentValues = getUserInput();

  # Hole mit der (POST)-Id Daten aus der Datenbank und vergleiche sie mit dem User-Input
  foreach ($currentValues as $item) {
    $dataFromDB = getNomenDataById($mysqli, $item['id']);
    $data[] = [
      'artikel' => $dataFromDB['artikel'],
      'nomen' => $dataFromDB['nomen'],
      'id' => (int) $item['id'],
    ];
  }
}

# -------------------------------------------------------------
# Fall x:
# keiner der vier oben genannten fälle trifft zu
# => folgende Daten werden gesetzt, um die Themen-Navigation 
# ohne Formular abzubilden


if ($case !== 1 && $case !== 2 && $case !== 3 && $case !== 4) {
  $themen = getThemes($mysqli);
  $data[] = [];
  $case = 1;
  $message = 'Bitte wähle ein Thema aus.';
}

# -------------------------------------------


# --- Datenbankverbindung schließen --------

mysqli_close($mysqli);

#----------- T W I G ------------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('/artikel.html.twig', [
  'username' => $username,
  'case' => $case,
  'title' => 'der, die, das',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath,
  'themen' => $themen,
  'formAction' => $formAction,
  'options' => $options,
  'data' => $data,
  'count' => count($data),
  'message' => $message,
  'page' => $page,
  'lastPage' => $lastPage
]);