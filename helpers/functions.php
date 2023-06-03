<?php
function myPost(string $name, int $length = 50)
{
  return trim(substr(filter_input(INPUT_POST, $name), 0, $length));
}


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

    # Hash-Wert aus AbteilungsNr generieren
    $hash = hash(MY_ALGO, $row['id'] . MY_SALT);

    # ---mehrdimensionaler Array für die Navigation ---------
    $themen[] = [
      'thema' => $row['thema'],
      'thema_id' => $row['id'],
      'href' => $_SERVER['SCRIPT_NAME'] . '?thema_id=' . $row['id'] . '&hash=' . $hash,
      'activeClass' => $activeClass
    ];
  }
  echo '<br>';
  // var_dump($themen);
  echo '<br>';

  echo '<br>';
  # $result freigeben 
  mysqli_free_result($result);

  return $themen;
}


//--------------------------------------------------------

function getDataWithId($mysqli, $thema_id)
{

  echo 'BIN in der Fkt getData';
  echo '<br>';
  echo $thema_id;
  echo '<br>';

  $sql = "SELECT nomen, artikel, id FROM nomen WHERE thema_id = $thema_id ORDER BY id LIMIT 10";
  $result = mysqli_query($mysqli, $sql);

  while ($row = mysqli_fetch_assoc($result)) {
    echo 'row: ' . $row['id'] . '  ';
    var_dump($row);
    echo '<br>';
    $data[] = [
      'artikel' => $row['artikel'],
      'nomen' => $row['nomen'],
      'id' => (int) $row['id'],
    ];
  }
  return $data;
}