<?php
require_once './helpers/functions.php';
!empty($_GET) ? $logout = $_GET['logout'] : $logout=false;
($logout == true) ? logout() : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="css/styles.css">
  <title>TeDD</title>
</head>

<body>
  <div class="site-wrapper">
    <header>
      <div class="header-left-side">
        <a href="./index.php"><img src="img/home1.png" alt="home" class="home-icon" width="30px" height="30px"></a>
      </div>
      <div class="header-title">
        <h1 class="header-h1">Teste Dein Deutsch</h1>
        <h2 class="header-h2">kleine Übungen für zwischendurch</h2>
      </div>
      <div class="header-right-side">
        <a class="login-link" href="login.php">login</a>
      </div>
    </header>
    <main>
      <div class="landingpage-content-wrapper">
        <div class="landingpage-column">
          <p>Keine Lust auf Benutzernamen und Passwörter?<br>
            Hier geht's direkt zu den Übungen!</p>
          <a href="uebungen.php" class="start">Start!</a>
        </div>
        <div class="landingpage-column">
          <p>Du hast ein Konto?</p>
          <a href="login.php">zum Login</a>
          <p>Lege ein Konto an und speichere Deinen Wissensstand.</p>
          <button>zur Registrierung</button>
        </div>

      </div>
    </main>
    <footer>
      &#169 2023
    </footer>
  </div>
</body>

</html>