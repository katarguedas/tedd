<?php 

require_once '../config.php';




# ------- Variablen fürs Template -----------

$formAction = $_SERVER['SCRIPT_NAME'];

$declination[] = [
  'der' => ['der', 'des', 'den', 'dem'],
  'das' => ['das', 'des', 'dem'],
  'die' => ['die', 'der'],
  'ohne' => ['der', 'das', 'den', 'die'],
  'plural' => ['die','der','den'],
  'ein' => ['ein','eines','einem','einen'],
  'eine' => ['eine','einer']
  ];  

$headerPath = './components/header.php';
$footerPath = './components/footer.php';

#----------- T W I G -----------------------

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);


#-------- Template rendern ------------------

echo $twig->render('/lueckentexte.html.twig', [
  'titel' => 'der, die, das',
  'incHeader' => $headerPath,
  'incFooter' => $footerPath
]);