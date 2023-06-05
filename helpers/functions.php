<?php
function myPost(string $name, int $length = 50)
{
  return trim(substr(filter_input(INPUT_POST, $name), 0, $length));
}

//-------------------------------------------------------------------

/**
 * getThemes: holt die verfügbaren Themen (Kategorien) aus der Datenbank,
 * um sie in der zweiten Navigation abzubilden 
 * @param object $mysqli
 * @param int $thema_id optional, wenn != -1, wird das vom User ausgewählte Thema hervorgehoben
 * @return array<array> Array mit Werten zur Darstellung der zweiten Navigazion
 */
function getThemes($mysqli, $thema_id = -1)
{
  # ---- Themen ids aus der Datenbank auslesen ------------

  $sql = "SELECT thema, id FROM thema ORDER BY id";
  $result = mysqli_query($mysqli, $sql);

  while ($row = mysqli_fetch_assoc($result)) {

    # ausgewähltes Thema bekommt zusötzliche css-Klasse

    if ($thema_id !== -1) {
      $activeClass = ($thema_id == $row['id']) ? 'sub-navigation-li-active' : '';
    } else {
      $activeClass = '';
    }

    # Hash-Wert aus thema_id generieren
    $hash = hash(MY_ALGO, $row['id'] . MY_SALT);

    # ---mehrdimensionaler Array für die Navigation ---------
    $themen[] = [
      'thema' => $row['thema'],
      'thema_id' => $row['id'],
      'href' => $_SERVER['SCRIPT_NAME'] . '?thema_id=' . $row['id'] . '&hash=' . $hash,
      'activeClass' => $activeClass
    ];
  }

  # $result freigeben 
  mysqli_free_result($result);

  return $themen;
}

//--------------------------------------------------------
function setLastPage($mysqli, $thema_id)
{
# Anzahl der Einträge in der Datenbank zu dem gewählten Thema
$itemsCount = getItemsCount($mysqli, $thema_id);
return ceil($itemsCount / ITEMS_PER_PAGE);
}

//--------------------------------------------------------

/**
 * getDataWithThemeId: holt für ein ausgewähltes Thema Daten aus der Datenbank, Tabelle nomen
 * @param object $mysqli
 * @param int $thema_id Id für das vom User ausgewählte Thema
 * @return array<array> Array mit Werten zur Darstellung der Übung für Artikel
 */
function getDataWithThemeId($mysqli, $thema_id, $page)
{
  $itemsPerPage = ITEMS_PER_PAGE;

  if ($page > 0) {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = "SELECT nomen, artikel, id FROM nomen WHERE thema_id = $thema_id ORDER BY id LIMIT $itemsPerPage OFFSET $offset";
    $result = mysqli_query($mysqli, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = [
        'artikel' => $row['artikel'],
        'nomen' => $row['nomen'],
        'id' => (int) $row['id'],
      ];
    }
  } else {
    $data[] = [];
  }

  return $data;
}

//--------------------------------------------------------
//--------------------------------------------------------


/**
 * getNomenDataById: holt einzelne Wertereihen aus der Datenbank
 * @param object $mysqli
 * @param int $id
 * @return array|bool|null
 */
function getNomenDataById($mysqli, $id)
{
  $sql = "SELECT artikel, nomen FROM nomen WHERE id = $id";
  $result = mysqli_query($mysqli, $sql);

  mysqli_num_rows($result) == 1 ? $row = mysqli_fetch_assoc($result) : $row = null;
  // $row = mysqli_fetch_assoc($result);
  return $row;
}


// ---------------------------------------------------

/**
 * getItemsCount: ermittelt die Anzahl an Einträgen 
 * in der Nomen-Tabelle zu einem bestimmten Thema
 * @param object $mysqli
 * @param int $thema_id
 * @return int
 */
function getItemsCount($mysqli, $thema_id) {

  $sql = "SELECT COUNT(*) AS anzahl FROM nomen WHERE thema_id = $thema_id";

  $result = mysqli_query($mysqli, $sql);

  $row = mysqli_fetch_assoc($result);
  return $row['anzahl'];
}

// ---------------------------------------------------

/**
 * getUserInput Holt Daten aus $_POST, prüft diese, wenn sie mit 'Artikel_' beginnen;
 *  speichert diese sowie die dazugehörigen Werte (User-Einbabe) 
 * @return array<array> Array, enthält die Artikel-Ids und die Usereingabe
 */
function getUserInput()
{
  foreach ($_POST as $id => $value) {
    if (str_starts_with($id, 'artikel_') && is_int((int) substr($id, 8)) && ((int) substr($id, 8)) > 0) {
      $currentValues[] = [
        'id' => (int) substr($id, 8),
        'userInput' => $value
      ];
    }
  }
  return $currentValues;
}


// ---------------------------------------------------
// ---------------------------------------------------

/**
 * checkUserInput: Prüft, ob der vom User ausgewählte Artikel korrekt ist oder nicht
 * @param string $userInput
 * @param string $artikel
 * @return bool
 */
function checkUserInput($userInput, $artikel)
{
  if ($userInput === $artikel)
    return true;
  else
    return false;
}


function login_check()
{
  session_start();
  if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    return false;
  } else {
    return true;
  }
}


function logout()
{
  # session übernehmen
  session_start();

  # session-daten löschen
  session_destroy();

  #ggf. session-cookie löschen und zwar so wie er gesetzt wurde(Path)
  setcookie('PHPSESSID', '', 0, '/');

}