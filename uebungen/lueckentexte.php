<?php

require_once '../config.php';
require_once '../helpers/functions.php';

# ---- Datenbankverbindung ------------------

require_once '../helpers/db-connection.php';


# ------- Variablen fürs Template -----------

$formAction = $_SERVER['SCRIPT_NAME'];

$declinations = [
  'der' => ['--','der', 'des', 'den', 'dem'],
  'das' => ['--','das', 'des', 'dem'],
  'die' => ['--','die', 'der'],
  'ohne' => ['--','der', 'das', 'den', 'die'],
  'plural' => '--',['die', 'der', 'den'],
  'ein' => ['--','ein', 'eines', 'einem', 'einen'],
  'eine' => ['--','eine', 'einer']

];


$case = 0;
$message = '';
$page = 0;
$lastPage = 0;

$incPath = [
  'incHeader' => './components/header.html.twig',
  'incFooter' => './components/footer.html.twig',
  'incNav' => './components/main-navigation.html.twig',
  'incPrevNext' => './components/prev-next.html.twig'
];

# -------------------------------------------

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

  $case = 1;

  # --- Daten für die Kategorien aus der Datenbank holen
  $categories = getCategories($mysqli);
  $data[] = [];
}

#--------------------------------------------------------------
# Fall 2:
# Kategorie gewählt, jetzt werden Daten aus der Datenbank geladen
# => die Übung startet
# -------------------------------------------------------------
# -- Daten per GET empfangen

if (!empty($_GET) && empty($_POST)) {

  $case = 2;

  $page = 1;
  $options;

  # neue Seitennummer per GET empfangen?
  (isset($_GET['page']) && $_GET['page'] > 0) ? $page = (int) filter_input(INPUT_GET, 'page') : $page = 1;

  # ist pagenummer kleiner 0: wird formular nicht dargestellt
  $page < 0 ? $case = 1 : $case = 2;

  # Thema-id per GET empfangen
  $category_id = (int) filter_input(INPUT_GET, 'kategorie_id');

  # Hashwert (Prüfsumme) 
  $gethash = filter_input(INPUT_GET, 'hash');

  # check, ob category-id nicht manipuliert wurde
  if (isset($category_id) && hash(MY_ALGO, $category_id . MY_SALTY) != $gethash) {
    $category_id = null;
    $case = 1; // damit wird das Formular nicht abgebildet
    $message = 'Bitte wähle eine Kategorie aus';
  } else {
    # Ermittele die Anzahl der Seiten, die 'durchgeblättert' werden können
    $lastPage = setLastPage($mysqli, $category_id, 2);
  }

  # Daten für die Kategorien aus der Datenbank holen,
  # wenn $category_id gesetzt, wird die gewählte Kategorie gehighlighted
  $categories = getCategories($mysqli, $category_id);

  # Daten für die Übung aus der Datenbank holen
  if (isset($category_id) && ($page <= $lastPage) && ($lastPage > 0)) {
    $data = getDataWithCategoryId($mysqli, $category_id, $page);
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

  #init-Wert
  $page = 1;

  # Thema_id per POST empfangen
  $category_id = (int) myPost('urlId', 7);

  # Daten für die Themen-Navigation aus der Datenbank holen,
  # wenn $thema_id gesetzt, wird das gewählte Thema gehighlighted
  $categories = getCategories($mysqli, $category_id);

  # Ermittele die Anzahl der Seiten, die 'durchgeblättert' werden können
  $lastPage = setLastPage($mysqli, $category_id, 2);

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


#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('/lueckentexte.html.twig', [
  'username' => $username,
  'titel' => 'Lückentexte',
  'inc' => $incPath,
  'pos' => 3,
  'categories' => $categories,
  'data' => $data,
  'declinations' => $declinations,
  'case' => $case,
  'page' => $page,
  'lastPage' => $lastPage
]);