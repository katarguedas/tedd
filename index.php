<?php 

require_once 'config.php';
require_once './helpers/functions.php';


$headerPath = './components/header.html.twig';
$footerPath = './components/footer.html.twig';

#-------------------------------------------

!empty($_GET) ? $logout = $_GET['logout'] : $logout=false;
($logout == true) ? logout() : null;

# User eingeloggt?'

$username='';
if(login_check()) {
  $username = $_SESSION['name'];
}

#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader);

#-------- Template rendern ------------------

echo $twig->render('index.html.twig', [
  'username' => $username,
  'titel' => 'TeDD',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath
]);