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
