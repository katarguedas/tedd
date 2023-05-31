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
    <?php include 'views/components/header.php'; ?>
    <main>
      <div class="landingpage-content-wrapper">
        <div class="landingpage-column">
          <p>Keine Lust auf Benutzernamen und Passwörter?<br>
            Hier geht's direkt zu den Übungen!</p>
          <!-- <p class="arrow-down"> &#8681;</p> -->
          <a href="views/uebungen.php" class="start" >Start!</a>
        </div>
        <div class="landingpage-column">
          <p>Du hast ein Konto?</p>
          <a  >zum Login</a>
          <p>Lege ein Konto an und speichere Deinen Wissensstand.</p>
          <button>zur Registrierung</button>
        </div>

      </div>
    </main>
    <?php include 'views/components/footer.php'; ?>
  </div>
</body>

</html>