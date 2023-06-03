<?php

require '../config.php';
require '../helpers/functions.php';

# ---- Datenbankverbindung ------------------
require '../helpers/db-connection.php';

# -------------------------------------------

$headerPath = './components/header.php';
$footerPath = './components/footer.php';

#----------- T W I G ------------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


# ------- Variablen fürs Template -----------

$formAction = $_SERVER['SCRIPT_NAME'];

$options = ['--', 'der', 'die', 'das'];

$case = 0;

# -------------------------------------------

# Fall 1:
# aller erster Seitenaufruf, noch keine Auswahl eines Themas erfolgt
# => Themen werden aufgelistget => zweite Nav
# -------------------------------------------------------------------
echo '<br>';
echo '<br>';
var_dump($_REQUEST);
echo '<br>';
echo '<br>';
echo 'ist GET gesetzt?';
var_dump($_GET);
echo '<br>';
echo '<br>';
echo 'ist POST gesetzt?';
var_dump($_POST);
echo '<br>';
echo '<br>';

if (empty($_GET) && empty($_POST)) {
  echo 'GET und POST sind empty';
  $case = 1;

  # Themen aus der Datenbank holen
  $themen = getThemes($mysqli);
}

#--------------------------------------------------------------

# Fall 2:
# Thema gewählt, jetzt werden Daten aus der Datenbank geladen
# => die Übung startet
# -------------------------------------------------------------
# -- Daten per GET empfangen
# Thema-id 
$thema_id = filter_input(INPUT_GET, 'thema_id');
# Hashwert (Prüfsumme) 
$gethash = filter_input(INPUT_GET, 'hash');

# check, ob thema-id nicht manipuliert wurde
if (isset($thema_id) && hash(MY_ALGO, $thema_id . MY_SALT) != $gethash) {
  $thema_id = null;
}

# --- Übungsdaten aus der Datenbank holen
$themen = getThemes($mysqli, $thema_id);
echo 'Thema_ID: '.$thema_id;
if (isset($thema_id)) {

  $data = getDataWithId($mysqli, $thema_id);

  echo '<br>';

  var_dump($data);
} else {
  $data[] = [];
}

# -------------------------------------------------------------
# Fall 3:
# 'prüfen' Button geklickt: 
# - selection des Users wird über POST übertragen, 
# - im hidden input kommen die ids der aktuellen Übung,
# => Auswertung der Daten und Darstellung des Ergebnisses für den User
# -------------------------------------------------------------
$button = myPost('button', 20);

if ($button === 'check') {
  echo 'Button prüfen geklickt';
  echo '<br>';

  $thema_id = myPost('urlId', 10);

  echo '<br>';
  echo '<br>';

  foreach ($_POST as $id => $value) {
    echo '';
    $currentIds[] = $id;
    $currentInput[] = $value;
    $currentValues[] = [
      'id' => $id,
      'userInput' => $value
    ];
  }

  function checkUserInput($currentValues) {

  }

  echo '<br>';
  echo 'currentValues: ';
  var_dump($currentValues);
  echo '<br>';
  echo '<br>';

  echo 'ids: ';
  echo '<br>';
  var_dump($currentIds);

  echo '<br>';
  echo 'input: ';
  echo '<br>';
  var_dump($currentInput);


  $themen = getThemes($mysqli, $thema_id);

  if (isset($thema_id)) {
  $data = getDataWithId($mysqli, $thema_id);
} else {
  $data[] = [];
}

  # -- daten prüfen --------


}

# -------------------------------------------------------------
# Fall 4:
# 'reset' Button geklickt:
# - die abgebildeten Übungen sollen beibehalten werden,
# - die User-Eingabe wird 'gelöscht' => gleiche Übung ohne Auswahl
# -------------------------------------------------------------




# ---- Formulardaten empfangen --------------


# ---- Formulardaten prüfen -----------------


# --- Formulardaten verarbeiten -------------


# -------------------------------------------



# --- Datenbankverbindung schließen --------

mysqli_close($mysqli);

#-------------------------------------------

$headerPath = './components/header.php';
$footerPath = './components/footer.php';

#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('/artikel.html.twig', [
  'case' => $case,
  'title' => 'der, die, das',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath,
  'themen' => $themen,
  'formAction' => $formAction,
  'options' => $options,
  'data' => $data,
  'count' => count($data)
]);