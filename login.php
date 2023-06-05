<?php

require_once 'config.php';
require_once './helpers/functions.php';

# ---- Datenbankverbindung ------------------

require_once './helpers/db-connection.php';

# -------------------------------------------

$headerPath = './components/header.html.twig';
$footerPath = './components/footer.html.twig';

# ------- Variablen fürs Template -----------

$formAction = $_SERVER['SCRIPT_NAME'];

#------------------
# User eingeloggt?'

$username='';
if(login_check()) {
  $username = $_SESSION['name'];
}

# ------ Formulardaten empfangen ------------


$email = trim(substr(filter_input(INPUT_POST, 'e-mail'), 0, 50));
$pwd = trim(substr(filter_input(INPUT_POST, 'pwd'), 0, 25));
$button = trim(substr(filter_input(INPUT_POST, 'button'), 0, 10));

var_dump($email);

if ($button == 'login') {
  # E-Mail escapen 
  $sqlemail = mysqli_escape_string($mysqli, $email);

  # SQL für User mit der E-Mail
  $sql = "SELECT * FROM users WHERE email = '$sqlemail'";
  $result = mysqli_query($mysqli, $sql);
  $row = mysqli_fetch_assoc($result);

  # gibt es einen Datensatz und ist das Passwort korrekt?
  if (mysqli_num_rows($result) == 1 && password_verify($pwd, $row['password'])) {

    $name = $row['name'];

    # Starte session
    session_start();

    # Speichere Daten in der Session
    // $_SESSION['email'] = $email;
    $_SESSION['name'] = $name;
    $_SESSION['login'] = true;

    # Weiterleiten zu den Übungen
    header('Location: uebungen.php');

  }
}

#----------- T W I G ------------------------

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader);
# -------------------------------------------


#-------- Template rendern ------------------

echo $twig->render('login.html.twig', [
  'username' => $username,
  'titel' => 'Login',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath,
  'formAction' => $formAction
]);