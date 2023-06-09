<?php 

# Autoloader-Funktion
function loadClass($classNAme) {
	require 'class/'.$classNAme.'.php';
}
# Autoloader-Funktion für PHP bekannt machen
spl_autoload_register('loadClass');


# Hashwerte
const MY_ALGO = 'sha1';
const MY_SALT = 'zrayop%?udi';


# Anzahl Items(Abrfagen) pro Seite
const ITEMS_PER_PAGE = 10;
const SETS_PER_PAGE = 5;