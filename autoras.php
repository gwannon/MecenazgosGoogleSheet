<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc/inc.php';

if(!isset($_REQUEST['log']) || $_REQUEST['log'] != 'no') registerLog("Autoras");
if(!isset($_REQUEST['log'])) loadCacheAutoras();

ob_start();

$stats = [
  "titulos" => 0,
  "titulos_solo_autores" => 0,
  "titulos_solo_autoras" => 0,
  "titulos_solo_autoresnb" => 0,
  "titulos_mixtos" => 0,
  "titulos_autores" => 0,
  "titulos_autoras" => 0,
  "titulos_autoresnb" => 0,
  "titulos_paginas" => 0,
  "titulos_autores_paginas" => 0,
  "titulos_autoras_paginas" => 0,
  "titulos_autoresnb_paginas" => 0
];
$editoriales = [];
$res = accessAuthorSheet(); ?>
<!doctype html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estudio sobre la publicación de títulos de rol de autoras y autores no binaries (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>)</title>
    <meta charset="UTF-8" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=VT323&display=swap" rel="stylesheet">
    <link rel="canonical" href="https://gwannon.com/mecenazgos/" />
    <meta name="description" content="Estudio sobre la publicación de títulos de rol de autoras y autores no binaries (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>). Actualizado a <?php echo UPDATE_DATE; ?>.">
    <meta property="og:title" content="Estudio sobre la publicación de títulos de rol de autoras y autores no binaries (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>)">
    <meta property="og:description" content="Estudio sobre la publicación de títulos de rol de autoras y autores no binaries (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>). Actualizado a <?php echo $res[0][7]['formattedValue']; ?>.">
    <meta property="og:url" content="https://gwannon.com/mecenazgos/" />
    <meta name="robots" content="noindex,nofollow" />
</head>
<body>
  <a href="#" class="accesible" title="Contraste ACTIVAR/DESACTIVAR">◐</a>
  <h1>Estudio sobre la publicación de títulos de rol de autoras y autores no binaries (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>)</h1>
  <h2><u>Última actualización:</u> <?php echo $res[0][8]['formattedValue']; ?></h2>
  <?php include("./autoras/presentacion.php"); ?>
  <h2>Años</h2>
  <p>Los datos de años en cursos son provisionales y pueden cambiar a lo largo del año. Los datos de años pasados son definitivos salvo algún error en su recogida.</p>
  <?php for ($i = 2026; $i <= (date("Y")+1); $i++) { ?>
    <a class="button" href="/mecenazgos/autoras.php?year=<?php echo $i; ?><?php echo ( isset($_GET['log']) ? '&log=no' : '' ); ?>"><?php echo $i; ?></a>
  <?php } ?>
  <h2>Gráficos con datos generales del <?php echo (strtolower (AUTHOR_SPREADSHEET_SHEET_NAME)); ?></h2>
  <div class="allcharts">
    <div style="width: 50%; max-width: 100%; min-width: 450px;">
      <canvas id="titulos-publicados"></canvas>
    </div>
    <div style="width: 50%; max-width: 100%; min-width: 450px;">
      <canvas id="paginas-publicadas"></canvas>
    </div>
  </div> 
  <h2>Listado de proyectos roleros publicados de autoras, autores no binaries y autores españoles durante el <?php echo (strtolower (AUTHOR_SPREADSHEET_SHEET_NAME)); ?></h2>
  <div class="tables">
    <table>
      <thead>
        <tr>
          <th>Nº</th>
          <th>Editorial</th>
          <th>Título</th>
          <th>Autoras</th>
          <th>Autores NB</th>
          <th>Autores</th>
          <th>Páginas</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($res as $key => $proyecto) { $label = custom_sanitize_title($proyecto[0]['formattedValue']); ?>
          <?php if ($key > 0) { 

            if(!isset($editoriales[$label])) {
              $editoriales[$label] = [
                "nombre" => $proyecto[0]['formattedValue'],
                "titulos" => 0,
                "titulos_solo_autores" => 0,
                "titulos_solo_autoras" => 0,
                "titulos_solo_autoresnb" => 0,
                "titulos_mixtos" => 0,
                "titulos_autores" => 0,
                "titulos_autoras" => 0,
                "titulos_autoresnb" => 0,
                "titulos_paginas" => 0,
                "titulos_autores_paginas" => 0,
                "titulos_autoras_paginas" => 0,
                "titulos_autoresnb_paginas" => 0
              ];
            }


            $stats['titulos'] ++;
            $editoriales[$label]['titulos'] ++;
            $stats['titulos_autores'] =  $stats['titulos_autores'] + $proyecto[3]['formattedValue'] + $proyecto[4]['formattedValue'];
            $editoriales[$label]['titulos_autores'] =  $editoriales[$label]['titulos_autores'] + $proyecto[3]['formattedValue'] + $proyecto[4]['formattedValue'];
            $stats['titulos_paginas'] = $stats['titulos_paginas'] + $proyecto[2]['formattedValue'];
            $editoriales[$label]['titulos_paginas'] = $editoriales[$label]['titulos_paginas'] + $proyecto[2]['formattedValue'];

            if(
              ($proyecto[3]['formattedValue'] > 0 && $proyecto[4]['formattedValue']) ||
              ($proyecto[4]['formattedValue'] > 0 && $proyecto[5]['formattedValue']) ||
              ($proyecto[3]['formattedValue'] > 0 && $proyecto[5]['formattedValue'])
            ) {
              $stats['titulos_mixtos'] ++;
              $editoriales[$label]['titulos_mixtos'] ++;
            } else if($proyecto[5]['formattedValue'] > 0) {
              $stats['titulos_solo_autoresnb'] ++;
              $editoriales[$label]['titulos_solo_autoresnb'] ++;
            } else if($proyecto[3]['formattedValue'] > 0) {
              $stats['titulos_solo_autoras'] ++;
              $editoriales[$label]['titulos_solo_autoras'] ++;
            } else if($proyecto[4]['formattedValue'] > 0) {
              $stats['titulos_solo_autores'] ++;
              $editoriales[$label]['titulos_solo_autores'] ++;
            }

            $total_autores = $proyecto[3]['formattedValue'] + $proyecto[4]['formattedValue'] + $proyecto[5]['formattedValue'];
            
            //si hay mujeres
            if($proyecto[3]['formattedValue'] > 0) {
              $stats['titulos_autoras'] = $stats['titulos_autoras'] + $proyecto[3]['formattedValue'];

              $editoriales[$label]['titulos_autoras'] = $editoriales[$label]['titulos_autoras'] + $proyecto[3]['formattedValue'];

              $stats['titulos_autoras_paginas'] = $stats['titulos_autoras_paginas'] + ($proyecto[3]['formattedValue'] * round($proyecto[2]['formattedValue'] / $total_autores, 2));

              $editoriales[$label]['titulos_autoras_paginas'] = $editoriales[$label]['titulos_autoras_paginas'] + ($proyecto[3]['formattedValue'] * round($proyecto[2]['formattedValue'] / $total_autores, 2));
            }

            //si hay hombres
            if($proyecto[4]['formattedValue'] > 0) {
              $stats['titulos_autores'] = $stats['titulos_autores'] + $proyecto[4]['formattedValue'];

              $editoriales[$label]['titulos_autores'] = $editoriales[$label]['titulos_autores'] + $proyecto[4]['formattedValue'];

              $stats['titulos_autores_paginas'] = $stats['titulos_autores_paginas'] + ($proyecto[4]['formattedValue'] * round($proyecto[2]['formattedValue'] / $total_autores, 2));

              $editoriales[$label]['titulos_autores_paginas'] = $editoriales[$label]['titulos_autores_paginas'] + ($proyecto[4]['formattedValue'] * round($proyecto[2]['formattedValue'] / $total_autores, 2));
            }

            //si hay NB
            if($proyecto[5]['formattedValue'] > 0) {
              $stats['titulos_autoresnb'] = $stats['titulos_autoresnb'] + $proyecto[5]['formattedValue'];

              $editoriales[$label]['titulos_autoresnb'] = $editoriales[$label]['titulos_autoresnb'] + $proyecto[5]['formattedValue'];

              $stats['titulos_autoresnb_paginas'] = $stats['titulos_autoresnb_paginas'] + ($proyecto[5]['formattedValue'] * round($proyecto[2]['formattedValue'] / $total_autores, 2));

              $editoriales[$label]['titulos_autoresnb_paginas'] = $editoriales[$label]['titulos_autoresnb_paginas'] + ($proyecto[5]['formattedValue'] * round($proyecto[2]['formattedValue'] / $total_autores, 2));
            }
          ?>
            <tr<?=(($proyecto[3]['formattedValue'] > 0 && $proyecto[4]['formattedValue']) || ($proyecto[4]['formattedValue'] > 0 && $proyecto[5]['formattedValue']) || ($proyecto[3]['formattedValue'] > 0 && $proyecto[5]['formattedValue']) ? " style='background-color: #ffa500; color: white;'" : ($proyecto[5]['formattedValue'] > 0 ? " style='background-color: #673AB7; color: white;'" : ($proyecto[3]['formattedValue'] > 0 ? " style='background-color: #008000; color: white;'": " style='background-color: red; color: white;'"))); ?>>
              <td style="text-align: left;"><?=$key; ?></td>
              <td style="text-align: left;"><?=$proyecto[0]['formattedValue']; ?></td>
              <td style="text-align: left;"><?=$proyecto[1]['formattedValue']; ?></td>
              <td><?=$proyecto[3]['formattedValue']; ?></td>
              <td><?=$proyecto[5]['formattedValue']; ?></td>
              <td><?=$proyecto[4]['formattedValue']; ?></td>
              <td><?=$proyecto[2]['formattedValue']; ?></td>
            </tr>              
          <?php } ?>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <p><b><span style="color: #008000;">Títulos publicados solo autoras</span> | <span style="color: #673AB7;">Títulos publicados solo autores no binaries</span> | <span style="color: red;">Títulos publicados solo autores</span> | <span style="color: #ffa500;">Títulos publicados mixtos</span></b></p>
  <script>
    new Chart(document.getElementById("titulos-publicados"), {
      type: 'pie',
      options: {
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Títulos publicados (<?=$stats['titulos']; ?>)',
            font: {
              size: 36,
              family: "VT323"
            }
          }
        }
      },
      data: { 
        labels: [
          'Títulos con solo autores (<?=round(($stats['titulos_solo_autores']/$stats['titulos'] * 100), 2); ?>%)',
          'Títulos con solo autoras (<?=round(($stats['titulos_solo_autoras']/$stats['titulos'] * 100), 2); ?>%)',
          'Títulos con solo autores NB (<?=round(($stats['titulos_solo_autoresnb']/$stats['titulos'] * 100), 2); ?>%)',
          'Títulos con equipos mixtos (<?=round(($stats['titulos_mixtos']/$stats['titulos'] * 100), 2); ?>%)'
        ],
        datasets: [{
          label: 'Títulos publicados: ',
          data: [
            <?=$stats['titulos_solo_autores']; ?>,
            <?=$stats['titulos_solo_autoras']; ?>,
            <?=$stats['titulos_solo_autoresnb']; ?>,
            <?=$stats['titulos_mixtos']; ?>
          ],
          backgroundColor: [
            'rgb(255, 0, 0)',
            'rgb(0, 128, 0)',
            'rgb(103, 58, 183)',
            'rgb(255, 165, 0)'
          ]
        }],
      }
    });

    new Chart(document.getElementById("paginas-publicadas"), {
      type: 'pie',
       options: {
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Páginas publicadas (<?=$stats['titulos_paginas']; ?>)',
            font: {
              size: 36,
              family: "VT323"
            }
          }
        }
      },
      data: { 
        labels: [
          'Páginas publicadas por autores (<?=round(($stats['titulos_autores_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%)',
          'Páginas publicadas por autoras (<?=round(($stats['titulos_autoras_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%)',
          'Páginas publicadas por autores NB (<?=round(($stats['titulos_autoresnb_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%)'
        ],
        datasets: [{
          label: 'Páginas: ',
          data: [
            <?=$stats['titulos_autores_paginas']; ?>,
            <?=$stats['titulos_autoras_paginas']; ?>,
            <?=$stats['titulos_autoresnb_paginas']; ?>
          ],
          backgroundColor: [
            'rgb(255, 0, 0)',
            'rgb(0, 128, 0)',
            'rgb(103, 58, 183)'
          ]
        }],
      }
    });
  </script>
  <h2>Datos por editorial</h2>
  <p>Aquí se muestran datos de editoriales que han publicado material de autores, autores NB y autoras españolas. Las editoriales que no aparecen aquí es porque no han publicado títulos que cumplan los requisitos para ser registrados en este estudio.</p>
  <div class="allchartseditorial">
    <h3>Títulos publicados</h3>
    <table>
      <thead>
        <tr>
          <th>Editorial</th>
          <th>Nº de títulos</th>
          <th colspan="2">Títulos con<br/>solo autores</th>
          <th colspan="2">Títulos con<br/>solo autoras</th>
          <th colspan="2">Títulos con<br/>solo autores NB</th>
          <th colspan="2">Títulos con<br/>equipos mixtos</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($editoriales as $label => $stats) { ?>
          <tr>
            <th><?=$stats['nombre']; ?></th>
            <td><?=$stats['titulos']; ?></td>

            <td<?=($stats['titulos_solo_autores'] > 0 ? " style='background-color: red; color: white;'" : ""); ?>><?=$stats['titulos_solo_autores']; ?></td>
            <td<?=($stats['titulos_solo_autores'] > 0 ? " style='background-color: red; color: white;'" : ""); ?>><?=round(($stats['titulos_solo_autores']/$stats['titulos'] * 100), 2); ?>%</td>

            <td<?=($stats['titulos_solo_autoras'] > 0 ? " style='background-color: #008000; color: white;'" : ""); ?>><?=$stats['titulos_solo_autoras']; ?></td>
            <td<?=($stats['titulos_solo_autoras'] > 0 ? " style='background-color: #008000; color: white;'" : ""); ?>><?=round(($stats['titulos_solo_autoras']/$stats['titulos'] * 100), 2); ?>%</td>

            <td<?=($stats['titulos_solo_autoresnb'] > 0 ? " style='background-color: #673AB7; color: white;'" : ""); ?>><?=$stats['titulos_solo_autoresnb']; ?></td>
            <td<?=($stats['titulos_solo_autoresnb'] > 0 ? " style='background-color: #673AB7; color: white;'" : ""); ?>><?=round(($stats['titulos_solo_autoresnb']/$stats['titulos'] * 100), 2); ?>%</td>

            <td<?=($stats['titulos_mixtos'] > 0 ? " style='background-color: #ffa500; color: white;'" : ""); ?>><?=$stats['titulos_mixtos']; ?></td>
            <td<?=($stats['titulos_mixtos'] > 0 ? " style='background-color: #ffa500; color: white;'" : ""); ?>><?=round(($stats['titulos_mixtos']/$stats['titulos'] * 100), 2); ?>%</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <h3>Páginas publicadas</h3>
    <table>
      <thead>
        <tr>
          <th>Editorial</th>
          <th>Páginas publicadas</th>
          <th colspan="2">Páginas publicadas<br/>por autores</th>
          <th colspan="2">Páginas publicadas<br/>por autoras</th>
          <th colspan="2">Páginas publicadas<br/>por autores NB</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($editoriales as $label => $stats) { ?>
          <tr>
            <th><?=$stats['nombre']; ?></th>
            <td><?=$stats['titulos_paginas']; ?></td>

            <td<?=($stats['titulos_autores_paginas'] > 0 ? " style='background-color: red; color: white;'" : ""); ?>><?=$stats['titulos_autores_paginas']; ?></td>
            <td<?=($stats['titulos_autores_paginas'] > 0 ? " style='background-color: red; color: white;'" : ""); ?>><?=round(($stats['titulos_autores_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%</td>

            <td<?=($stats['titulos_autoras_paginas'] > 0 ? " style='background-color: #008000; color: white;'" : ""); ?>><?=$stats['titulos_autoras_paginas']; ?></td>
            <td<?=($stats['titulos_autoras_paginas'] > 0 ? " style='background-color: #008000; color: white;'" : ""); ?>><?=round(($stats['titulos_autoras_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%</td>

            <td<?=($stats['titulos_autoresnb_paginas'] > 0 ? " style='background-color: #673AB7; color: white;'" : ""); ?>><?=$stats['titulos_autoresnb_paginas']; ?></td>
            <td<?=($stats['titulos_autoresnb_paginas'] > 0 ? " style='background-color: #673AB7; color: white;'" : ""); ?>><?=round(($stats['titulos_autoresnb_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <p><b><span style="color: #008000;">Editoriales con títulos publicados solo autoras</span> | <span style="color: #673AB7;">Editoriales con títulos publicados solo autores no binaries</span> | <span style="color: red;">Editoriales con títulos publicados solo autores</span> | <span style="color: #ffa500;">Editoriales con títulos publicados mixtos</span></b></p>
  <?php include("./autoras/".AUTHOR_SPREADSHEET_SHEET_YEAR.".php"); ?>
  <h3>Origen de este proyecto</h3>
  <p>Este proyecto surge de un vídeo de <a href="https://www.youtube.com/watch?v=KvTpwn6RNas&t=2985s">Friki Vetusto</a> en el que se presentaban las nominaciones a los Premios Vestutini 2025. En un momento dado se comenta que no había mujeres en las nominaciones, porque las editoriales no habían publicado a mujeres. Ante esas declaraciones, que me parecieron al principio muy fuertes, me pico la curiosidad y decidí a hacer la estadística para este año 2026.</p>
  <h3>Código abierto</h3>
  <p>Todo el código de la web puedes encontrarlo en <a href="https://github.com/gwannon/MecenazgosGoogleSheet" target="_blank">GitHub</a> con licencia GNU General Public License v3.0</a>.</p>
  <style>
      <?php echo file_get_contents(__DIR__ . '/inc/style.css'); ?>
  </style>
  <script>
    $('.accesible').on('click', function(e) {
      e.preventDefault();
      $('body').toggleClass("acc");
    });
  </script>
</body>
</html>
<?php $html = ob_get_clean();
saveCacheAutoras($html);
echo $html;