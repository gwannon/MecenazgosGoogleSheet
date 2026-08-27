<?php

define('SPREADSHEET_ID', 'XXXXXXXXXXXXXXXXXX'); //Se saca de la URL del Google Sheet
define('SPREADSHEET_SHEET_NAME', 'XXXXXXXXXXXX'); //Nombre de la hoja de Google Sheet donde están los datos
define('UPDATE_DATE', '01/01/1970'); //Fecha de última actualziación de los datos
define('EXPIRE_CACHE', 5*60); // 5 Minutos

define('AUTHOR_SPREADSHEET_ID', 'XXXXXXXXXXXXXXXXXX'); //Se saca de la URL del Google Sheet
define('AUTHOR_SPREADSHEET_SHEET_NAME', 'Año '.(isset($_GET['year']) && is_numeric($_GET['year']) && $_GET['year'] > 2025 && $_GET['year'] <= date("Y")  ? strip_tags($_GET['year']) : "2026")); //Nmbre de la hoja de Google Sheet donde están los datos
define('AUTHOR_UPDATE_DATE', '01/01/1970'); //Fecha de última actualización de los datos

date_default_timezone_set("Europe/Madrid");