<?php 

require_once 'config.php';
require_once './helpers/functions.php';


$headerPath = './components/header.html.twig';
$footerPath = './components/footer.html.twig';

$navPath = './components/main-navigation.html.twig';
#-------------------------------------------
// 

# User eingeloggt?'

$username='';
if(login_check()) {
  $username = $_SESSION['name'];
}

#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('uebungen.html.twig', [
  'username' => $username,
  'titel' => 'Übungen',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath,
  'incNav' => $navPath,
]);