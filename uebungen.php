<?php 

require_once 'config.php';


$headerPath = './components/header.html.twig';
$footerPath = './components/footer.html.twig';

#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('uebungen.html.twig', [
  'titel' => 'Übungen',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath
]);