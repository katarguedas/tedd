<?php

/**
 * getGroups: Verfügbare Themen/Kategorien-Groupen bzw. deren Ids für Lückentexte aus der Datenbank holen
 * @param object $mysqli
 * @param int $id
 * @return array<array>
 */

 function getGroups($mysqli, $tableName, $id = -1)
 {
   $tableName === 'kategorien' ? $value = 'kategorie' : null;
   $tableName === 'thema' ? $value = 'thema' : null;
 
   # ---- Themen ids aus der Datenbank auslesen ------------
   $sql = "SELECT $value, id FROM $tableName ORDER BY id";
 
   try {
     $result = mysqli_query($mysqli, $sql);
 
     while ($row = mysqli_fetch_assoc($result)) {
 
       # ausgewählte Kategorie bekommt zusötzliche css-Klasse
       if ($id !== -1) {
         $activeClass = ($id == $row['id']) ? 'sub-navigation-li-active' : '';
       } else {
         $activeClass = '';
       }
 
       # Hash-Wert aus thema_id generieren
       $hash = hash(MY_ALGO, $row['id'] . MY_SALT);
 
       # ---mehrdimensionaler Array für die Navigation ---------
       $groups[] = [
         'group' => $row[$value],
         'group_id' => $row['id'],
         'href' => $_SERVER['SCRIPT_NAME'] . '?group_id=' . $row['id'] . '&hash=' . $hash,
         'activeClass' => $activeClass
       ];
     }
 
     # $result freigeben 
     mysqli_free_result($result);
 
     return $groups;
   } catch (Exception) {
     echo 'Daten konnten nicht geladen werden';
     return [];
   }
 }
 
#--------------------------------------------------------------------------
 
/**
 * getDataWithThemeId: holt für ein ausgewähltes Thema Daten aus der Datenbank, Tabelle nomen
 * @param object $mysqli
 * @param int $id Id für das vom User ausgewählte Thema
 * @param int $page
 * @return array<array> Array mit Werten zur Darstellung der Übung für Artikel
 */
function getDataWithThemeId($mysqli, $id, $page)
{
  $itemsPerPage = ITEMS_PER_PAGE;

  if ($page > 0) {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = "SELECT nomen, artikel, plural, id FROM nomen WHERE thema_id = $id ORDER BY id LIMIT $itemsPerPage OFFSET $offset";
    $result = mysqli_query($mysqli, $sql);

// hier Unterschiede!!!!!!
    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = [
        'artikel' => $row['artikel'],
        'nomen' => $row['nomen'],
        'plural' => $row['plural'],
        'id' => (int) $row['id'],
      ];
    }
  } else {
    $data[] = [];
  }

  return $data;
}


#---------------------------------------------------------------

/**
 * getDataWithCategoryId: holt Daten für Lückentexte aus der Datenbank
 * @param object $mysqli
 * @param int $id
 * @param int $page
 * @return array
 */
function getDataWithCategoryId($mysqli, $id, $page)
{
  # Anzahl der Sätze pro 'Seite'
  $setsPerPage = SETS_PER_PAGE;

  if ($page > 0) {
    $offset = ($page - 1) * $setsPerPage;
    $sql = "SELECT textteil1, textteil2, textteil3, loesung, id FROM artikeltexte WHERE kategorie_id = $id ORDER BY id LIMIT $setsPerPage OFFSET $offset";
    $result = mysqli_query($mysqli, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = [
        'textPart1' => $row['textteil1'],
        'textPart2' => $row['textteil2'],
        'textPart3' => $row['textteil3'],
        'solution' => $row['loesung'],
        'id' => (int) $row['id'],
      ];
    }
  } else {
    $data[] = [];
  }
  return $data;
}

#---------------------------------------------------------------

/**
 * getNomenDataById: holt einzelne Wertereihen aus der Datenbank
 * @param object $mysqli
 * @param int $id
 * @return array|bool|null
 */
function getNomenDataById($mysqli, $id)
{
  $sql = "SELECT artikel, nomen, plural FROM nomen WHERE id = $id";
  $result = mysqli_query($mysqli, $sql);

  mysqli_num_rows($result) == 1 ? $row = mysqli_fetch_assoc($result) : $row = null;
  // $row = mysqli_fetch_assoc($result);
  return $row;
}

// ---------------------------------------------------

/**
 * getTextDataById: holt für bestimmte Id die Daten für den Lückentext aus der Datenbank
 * @param object $mysqli
 * @param int $id
 * @return array|bool|null
 */
function getTextDataById($mysqli, $id)
{
  $sql = "SELECT textteil1, textteil2, textteil3, loesung, id FROM artikeltexte WHERE id = $id";
  try {
    $result = mysqli_query($mysqli, $sql);

    mysqli_num_rows($result) == 1 ? $row = mysqli_fetch_assoc($result) : $row = null;
    return $row;
  } catch (Exception $errorMessage) {
    // echo $errorMessage->getMessage();
    return null;
  }
}

//--------------------------------------------------------

/**
 * getItemsCount: ermittelt die Anzahl an Einträgen 
 * in der Nomen-Tabelle zu einem bestimmten Thema
 * @param object $mysqli
 * @param int $id
 * @param int $tb
 * @return int
 */
function getItemsCount($mysqli, $id, $tb)
{
  if ($tb === 1)
    $sql = "SELECT COUNT(*) AS anzahl FROM nomen WHERE thema_id = $id";
  if ($tb === 2)
    $sql = "SELECT COUNT(*) AS anzahl FROM artikeltexte WHERE kategorie_id = $id";

  $result = mysqli_query($mysqli, $sql);
  $row = mysqli_fetch_assoc($result);
  return $row['anzahl'];
}

//--------------------------------------------------------

/**
 * setLastPage: ermittelt, auf wie vielen Seiten die Übungen dargestellt werden
 * @param object $mysqli
 * @param int $id
 * @param int $tb Faktor zur unterscheidung der Übungen
 * @return int
 */
function setLastPage($mysqli, $id, $tb)
{
  # Anzahl der Einträge in der Datenbank zm gewähltem Thema
  $itemsCount = getItemsCount($mysqli, $id, $tb);

  if ($tb === 1) // Artikel, Plural
    return ceil($itemsCount / ITEMS_PER_PAGE);
  if ($tb === 2) // Lückentexte
    return (int) ceil($itemsCount / SETS_PER_PAGE);
  return 1;
}