<?php 

require_once '../config.php';


$headerPath = './components/header.php';
$footerPath = './components/footer.php';

#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('/singular-plural.html.twig', [
  'titel' => 'der, die, das',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath
]);