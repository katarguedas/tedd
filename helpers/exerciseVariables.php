<?php 

$incPath = [
  'incHeader' => './components/header.html.twig',
  'incFooter' => './components/footer.html.twig',
  'incNav' => './components/main-navigation.html.twig',
  'incPrevNext' => './components/prev-next.html.twig'
];

$formAction = $_SERVER['SCRIPT_NAME'];

$case = 0;
$message = '';
$page = 0;
$lastPage = 0;
