<?php

/**
 * liest einen POST-Parameter aus und gibt ihn gefiltert zurück
 * @param string $name Name des POST-Parameters
 * @param int $laenge [optional] Anzahl der Zeichen
 * @return string der gefilterte Wert
 */
function myPost(string $name, int $length = 50)
{
  return trim(substr(filter_input(INPUT_POST, $name), 0, $length));
}

// ---------------------------------------------------

/**
 * getUserInput Holt Daten aus $_POST, prüft diese, wenn sie mit 'Artikel_' beginnen;
 *  speichert diese sowie die dazugehörigen Werte (User-Einbabe) 
 * @return array<array> Array, enthält die Artikel-Ids und die Usereingabe
 */
function getUserInput($startString)
{
  foreach ($_POST as $key => $value) {
    $startStringLength = strlen($startString) + 1;

    if (
      str_starts_with($key, $startString . '_') &&
      is_int((int) substr($key, $startStringLength)) &&
      ((int) substr($key, $startStringLength)) > 0
    ) {
      $currentValues[] = [
        'id' => (int) substr($key, $startStringLength),
        'userInput' => $value
      ];
    }
  }
  return $currentValues;
}

// ---------------------------------------------------

/**
 * checkUserInput: Prüft, ob der vom User ausgewählte Artikel korrekt ist oder nicht
 * @param string $userInput
 * @param string $artikel
 * @return bool
 */
function checkUserInput($userInput, $solution)
{
  if ($userInput === $solution)
    return true;
  else
    return false;
}

// ---------------------------------------------------


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