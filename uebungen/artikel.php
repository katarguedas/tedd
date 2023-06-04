<?php

require_once '../config.php';
require_once '../helpers/functions.php';

# ---- Datenbankverbindung ------------------

require_once '../helpers/db-connection.php';

# -------------------------------------------

$headerPath = './components/header.html.twig';
$footerPath = './components/footer.html.twig';

#----------- T W I G ------------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


# ------- Variablen fürs Template -----------

$formAction = $_SERVER['SCRIPT_NAME'];
$options = ['--', 'der', 'die', 'das'];
$case = 0;
$message = '';

$username='';
if(login_check()) {
  $username = $_SESSION['name'];
}

# -------------------------------------------

# Fall 1:
# aller erster Seitenaufruf, noch keine Auswahl eines Themas erfolgt
# => Themen werden aufgelistget => zweite Nav
# -------------------------------------------------------------------

// echo 'ist GET gesetzt?';
// var_dump($_GET);
// echo '<br>';
// echo 'ist POST gesetzt?';
// var_dump($_POST);
// echo '<br>';

if (empty($_GET) && empty($_POST)) {
  // echo 'GET und POST sind empty';
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
  # Thema-id 
  $thema_id = (int) filter_input(INPUT_GET, 'thema_id');
  # Hashwert (Prüfsumme) 
  $gethash = filter_input(INPUT_GET, 'hash');

  # check, ob thema-id nicht manipuliert wurde
  if (isset($thema_id) && hash(MY_ALGO, $thema_id . MY_SALT) != $gethash) {
    $thema_id = null;
    $case = 1; // damit wird das Formular nicht abgebildet
    $message = 'Bitte wähle ein Thema aus';
  }

  # Daten für die Themen-Navigation aus der Datenbank holen,
  # wenn $thema_id gesetzt, wird das gewählte Thema gehighlighted
  $themen = getThemes($mysqli, $thema_id);

  if (isset($thema_id)) {
    $data = getDataWithThemeId($mysqli, $thema_id);
  } else {
    $data[] = [];
  }
}

# -------------------------------------------------------------
# Fall 3:
# 'prüfen' Button geklickt: 
# - selection des Users wird über POST übertragen, 
# - im hidden input kommen die ids der aktuellen Übung,
# => Auswertung der Daten und Darstellung des Ergebnisses für den User
# -------------------------------------------------------------

if (empty($_GET) && !empty($_POST) && myPost('button', 5) === 'check') {
  $case = 3;

  # Daten per POST empfangen
  $thema_id = (int) myPost('urlId', 10);

  # Daten für die Themen-Navigation aus der Datenbank holen,
  # wenn $thema_id gesetzt, wird das gewählte Thema gehighlighted
  $themen = getThemes($mysqli, $thema_id);

  # Prüfe die User-Eingabe für Artikel und hole sie sowie die aktuellen Artikel-Ids aus $_POST:
  $currentValues = getUserInput();

  # Hole mit der (POST)-Id Daten aus der Datenbank und vergleiche sie mit dem User-Input
  foreach ($currentValues as $item) {
    $dataFromDB = getNomenDataById($mysqli, $item['id']);
    $userResult = checkUserInput($item['userInput'], $dataFromDB['artikel']);

    $data[] = [
      'artikel' => $dataFromDB['artikel'],
      'nomen' => $dataFromDB['nomen'],
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
  $thema_id = (int) myPost('urlId', 10);
  $themen = getThemes($mysqli, $thema_id);

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

#-------------------------------------------



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
]);