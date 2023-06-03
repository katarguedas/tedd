<?php 

require 'config.php';


$headerPath = './components/header.php';
$footerPath = './components/footer.php';

#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('uebungen.html.twig', [
  'titel' => 'Übungen',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath
]);