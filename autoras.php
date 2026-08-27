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
  "titulos_mixtos" => 0,
  "titulos_autores" => 0,
  "titulos_autoras" => 0,
  "titulos_paginas" => 0,
  "titulos_autoras_paginas" => 0
];
$editoriales = [];
$res = accessAuthorSheet(); ?>
<!doctype html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estudio sobre la publicación de títulos de rol de autoras (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>)</title>
    <meta charset="UTF-8" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=VT323&display=swap" rel="stylesheet">
    <link rel="canonical" href="https://gwannon.com/mecenazgos/" />
    <meta name="description" content="Estudio sobre la publicación de títulos de rol de autoras (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>). Actualizado a <?php echo UPDATE_DATE; ?>.">
    <meta property="og:title" content="Estudio sobre la publicación de títulos de rol de autoras (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>)">
    <meta property="og:description" content="Estudio sobre la publicación de títulos de rol de autoras (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>). Actualizado a <?php echo $res[0][7]['formattedValue']; ?>.">
    <meta property="og:url" content="https://gwannon.com/mecenazgos/" />
</head>
<body>
  <a href="#" class="accesible" title="Contraste ACTIVAR/DESACTIVAR">◐</a>
  <h1>Estudio sobre la publicación de títulos de rol de autoras (<?php echo AUTHOR_SPREADSHEET_SHEET_NAME; ?>)</h1>
  <h2><u>Última actualización:</u> <?php echo $res[0][7]['formattedValue']; ?></h2>
  <p>Este estudio trata de mostrar el papel de las autoras de material rolero en España anualmente, empezando por el <?php echo (strtolower (AUTHOR_SPREADSHEET_SHEET_NAME)); ?>.</p>
  <p>A la hora de elaborar este estudio se han seguido las siguientes reglas y parámetros:</p>
  <ol>
    <li>Se han registrado los productos roleros (juegos, aventuras, suplementos, ensayos teóricos, manuales de técnicas roleras, etc.) escritos por autoras y autores españoles y publicados por editoriales y grupos creativos. No importa el idioma en que e escriba, castellano, catalán, euskera, galego, inglés, etc.</li>
    <li>Para el estudio buscamos que haya una intención de publicar el título en sí mismo, no como un producto añadido a otro con carácter gratuito y/o promocional. Es por ello que solo se registran <b>productos publicados físicamente de 16 o más páginas y con PVP</b>. Se busca excluir material de promoción que, por ejemplo, ofrezcan gratis las editoriales en determinadas ocasiones o recompensas especiales de campañas de mecenazgo.</li>
    <li>Se han <b>excluido títulos auto-publicados</b> porque el estudio busca reflejar como se relacionan editoriales/grupos creativos con autoras y autores, relación que en los auto-publicados no se da.</li>
    <li>Se considera <b>autor o autora aquella persona que la editorial/grupo creativo ha promocionado como autor o autora de la publicación</b>. En caso de no quedar claro, se ha considerado autor/es al escritor o escritores principales y se han excluido a personas que solo han trabajado en su edición, maquetación, corrección, ilustración y/o promoción.</li>
    <li>Se usa <b>el número de páginas impresas para estimar la importancia que da la editorial a la obra y a la autora/autor</b>. No es un sistema perfecto, pero es mucho más realista que simplemente contabilizar el número de obras publicadas. Cuando unas obras tiene varias personas autoras se ha dividido el número de páginas entre el número de autores a la hora de contabilizar cuántas páginas ha escrito cada autora o autor.</li>
    <li>Se ha usado el género atribuido a cada nombre propio para determinar si estamos ante una autora o un autor. No es el sistema ideal, pero a falta de pronombres (y su uso en el material promocional) es lo más lejos que se ha podido llegar sin tener que preguntar directamente a les autores e inmiscuirse en la privacidad de las personas. <b>Sentimos no poder representar en este estudio la participación de autores no-binarios/queer.</b></li>
  </ol>
  <p>El estudio es algo vivo que se actualiza según pasan los meses hasta poder tener una idea general del año al terminar este. Si bien, hasta finalizado el año no se tendrán datos definitivos y totales, sí permiten ver tendencias.</p> 
  <p>El estudio puede tener títulos que han sido anunciados y también que se supone que publicarán en el año. En caso de que luego no se cumpla la fecha, de forma que si al acabar el año o la editorial anunciar otra fecha, serán movidos a otro año. Estos cambios son automáticos y quedan registrados de forma que junto a cada estudio vendrá la fecha de la última actualización del proyecto.</p>
  <p style="border: 1px solid var(--main-color); padding: 5px;">Si detectas datos desactualizados o crees que falta algún título publicado, puedes ponerte en contacto conmigo a través de <a href="mailto:monclus.jorge+autoras@gmail.com">monclus.jorge@gmail.com</a>.</p> 
  <h2>Listado de proyectos roleros publicados de autoras y autores epañoles durante el <?php echo (strtolower (AUTHOR_SPREADSHEET_SHEET_NAME)); ?></h2>
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
                "titulos_mixtos" => 0,
                "titulos_autores" => 0,
                "titulos_autoras" => 0,
                "titulos_paginas" => 0,
                "titulos_autoras_paginas" => 0
              ];
            }


            $stats['titulos'] ++;
            $editoriales[$label]['titulos'] ++;
            $stats['titulos_autores'] =  $stats['titulos_autores'] + $proyecto[3]['formattedValue'] + $proyecto[4]['formattedValue'];
            $editoriales[$label]['titulos_autores'] =  $editoriales[$label]['titulos_autores'] + $proyecto[3]['formattedValue'] + $proyecto[4]['formattedValue'];
            $stats['titulos_paginas'] = $stats['titulos_paginas'] + $proyecto[2]['formattedValue'];
            $editoriales[$label]['titulos_paginas'] = $editoriales[$label]['titulos_paginas'] + $proyecto[2]['formattedValue'];

            if($proyecto[3]['formattedValue'] > 0 && $proyecto[4]['formattedValue']) {
              $stats['titulos_mixtos'] ++;
              $editoriales[$label]['titulos_mixtos'] ++;
            } else if($proyecto[3]['formattedValue'] > 0) {
              $stats['titulos_solo_autoras'] ++;
              $editoriales[$label]['titulos_solo_autoras'] ++;
            } else if($proyecto[4]['formattedValue'] > 0) {
              $stats['titulos_solo_autores'] ++;
              $editoriales[$label]['titulos_solo_autores'] ++;
            }
            
            //si hay mujeres
            if($proyecto[3]['formattedValue'] > 0) {
              $stats['titulos_autoras'] = $stats['titulos_autoras'] + $proyecto[3]['formattedValue'];
              $editoriales[$label]['titulos_autoras'] = $editoriales[$label]['titulos_autoras'] + $proyecto[3]['formattedValue'];
              $stats['titulos_autoras_paginas'] = $stats['titulos_autoras_paginas'] + round($proyecto[2]['formattedValue'] / ($proyecto[3]['formattedValue'] + $proyecto[4]['formattedValue']), 2);
              $editoriales[$label]['titulos_autoras_paginas'] = $editoriales[$label]['titulos_autoras_paginas'] + round($proyecto[2]['formattedValue'] / ($proyecto[3]['formattedValue'] + $proyecto[4]['formattedValue']), 2);
            }
          ?>
            <tr<?=($proyecto[3]['formattedValue'] > 0 && $proyecto[4]['formattedValue'] > 0 ? " style='background-color: #ffa500; color: white;'" : ($proyecto[3]['formattedValue'] > 0 ? " style='background-color: #008000; color: white;'" : " style='background-color: red; color: white;'")); ?>>
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
  <p><b><span style="color: #008000;">Títulos publicados solo autoras</span> | <span style="color: red;">Títulos publicados solo autores</span> | <span style="color: #ffa500;">Títulos publicados mixtos</span></b></p>
  <h2>Gráficos con datos generales</h2>
  <div class="allcharts">
    <div style="width: 33.33%; max-width: 100%; min-width: 350px;">
      <h3>Títulos publicados (<?=$stats['titulos']; ?>)</h3>
      <canvas id="titulos-publicados"></canvas>
    </div>
    <script>
      new Chart(document.getElementById("titulos-publicados"), {
        type: 'pie',
        data: { 
          labels: [
            'Títulos con solo autores (<?=round(($stats['titulos_solo_autores']/$stats['titulos'] * 100), 2); ?>%)',
            'Títulos con solo autoras (<?=round(($stats['titulos_solo_autoras']/$stats['titulos'] * 100), 2); ?>%)',
            'Títulos con equipos mixtos (<?=round(($stats['titulos_mixtos']/$stats['titulos'] * 100), 2); ?>%)'
          ],
          datasets: [{
            label: 'Títulos publicados: ',
            data: [
              <?=$stats['titulos_solo_autores']; ?>,
              <?=$stats['titulos_solo_autoras']; ?>,
              <?=$stats['titulos_mixtos']; ?>
            ],
            backgroundColor: [
              'rgb(255, 0, 0)',
              'rgb(0, 128, 0)',
              'rgb(255, 165, 0)'
            ]
          }],
        }
      });
    </script>
    <div style="width: 33.33%; max-width: 100%; min-width: 350px;">
      <h3>Páginas publicadas (<?=$stats['titulos_paginas']; ?>)</h3>
      <canvas id="paginas-publicadas"></canvas>
    </div>
    <script>
      new Chart(document.getElementById("paginas-publicadas"), {
        type: 'pie',
        data: { 
          labels: [
            'Páginas publicadas por autores (<?=round((($stats['titulos_paginas'] - $stats['titulos_autoras_paginas'])/$stats['titulos_paginas'] * 100), 2); ?>%)',
            'Páginas publicadas por autoras (<?=round(($stats['titulos_autoras_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%)'
          ],
          datasets: [{
            label: 'Páginas: ',
            data: [
              <?=($stats['titulos_paginas'] - $stats['titulos_autoras_paginas']); ?>,
              <?=$stats['titulos_autoras_paginas']; ?>
            ],
            backgroundColor: [
              'rgb(255, 0, 0)',
              'rgb(0, 128, 0)'
            ]
          }],
        }
      });
    </script> 
  </div>
  <h2>Datos por editorial</h2>
  <p>Aquí se muestran datos de editoriales que han publicado material de autores y autoras españolas. Las editoriales que no aparecen aquí es porque no han publicado títulos que cumplan los requisitos para ser registrados en este estudio.</p>
  <div class="allchartseditorial">
    <table>
      <thead>
        <tr>
          <th>Editorial</th>
          <th>Nº de títulos</th>
          <th colspan="2">Títulos con<br/>solo autores</th>
          <th colspan="2">Títulos con<br/>solo autoras</th>
          <th colspan="2">Títulos con<br/>equipos mixtos</th>
          <th>Páginas publicadas</th>
          <th colspan="2">Páginas publicadas<br/>por autores</th>
          <th colspan="2">Páginas publicadas<br/>por autoras</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($editoriales as $label => $stats) { ?>
          <tr<?=($stats['titulos_mixtos'] > 0 ? " style='background-color: #ffa500; color: white;'" : ($stats['titulos_solo_autoras'] > 0 ? " style='background-color: #008000; color: white;'" : " style='background-color: red; color: white;'")); ?>>
            <th><?=$stats['nombre']; ?></th>
            <td><?=$stats['titulos']; ?></td>
            <td><?=$stats['titulos_solo_autores']; ?></td>
            <td><?=round(($stats['titulos_solo_autores']/$stats['titulos'] * 100), 2); ?>%</td>
            <td><?=$stats['titulos_solo_autoras']; ?></td>
            <td><?=round(($stats['titulos_solo_autoras']/$stats['titulos'] * 100), 2); ?>%</td>
            <td><?=$stats['titulos_mixtos']; ?></td>
            <td><?=round(($stats['titulos_mixtos']/$stats['titulos'] * 100), 2); ?>%</td>

            <td><?=$stats['titulos_paginas']; ?></td>
            <td><?=($stats['titulos_paginas'] - $stats['titulos_autoras_paginas']); ?></td>
            <td><?=round((($stats['titulos_paginas'] - $stats['titulos_autoras_paginas'])/$stats['titulos_paginas'] * 100), 2); ?>%</td>
            <td><?=$stats['titulos_autoras_paginas']; ?></td>
            <td><?=round(($stats['titulos_autoras_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%</td>
          </tr>

          <?php /*<div style="width: calc(33.33% - 30px); max-width: 100%; min-width: 320px;">
            <h3>Páginas publicadas por <?=$stats['nombre']; ?> (<?=$stats['titulos_paginas']; ?>)</h3>
            <canvas id="paginas-publicadas-<?=$label; ?>"></canvas>
          </div>
          <script>
            new Chart(document.getElementById("paginas-publicadas-<?=$label; ?>"), {
              type: 'pie',
              data: { 
                labels: [
                  'Páginas publicadas por autores (<?=round((($stats['titulos_paginas'] - $stats['titulos_autoras_paginas'])/$stats['titulos_paginas'] * 100), 2); ?>%)',
                  'Páginas publicadas por autoras (<?=round(($stats['titulos_autoras_paginas']/$stats['titulos_paginas'] * 100), 2); ?>%)'
                ],
                datasets: [{
                  label: 'Páginas: ',
                  data: [
                    <?=($stats['titulos_paginas'] - $stats['titulos_autoras_paginas']); ?>,
                    <?=$stats['titulos_autoras_paginas']; ?>
                  ],
                  backgroundColor: [
                    'rgb(255, 0, 0)',
                    'rgb(0, 128, 0)'
                  ]
                }],
              }
            });
          </script> */ ?>


        <?php } ?>
      </tbody>
    </table>
  </div>
  <p><b><span style="color: #008000;">Editoriales con títulos publicados solo autoras</span> | <span style="color: red;">Editoriales con títulos publicados solo autores</span> | <span style="color: #ffa500;">Editoriales con títulos publicados mixtos</span></b></p>
  <h2>Conclusiones</h2>
  <p>Sobre las conclusiones, no me considero tan experto en la materia como para sacar unas medianamente válidas. El objetivo es plasmar una realidad y luego dejar a los sujetos de este estudio que saquen sus propias conclusiones.</p>
  <p>Además, debido a que no existe un censo rolero que nos pueda dar la realidad de la afición en cuestiones de género (tanto de aficionades como de profesionales), solo puedo ofrecer los datos que puedo sacar de las publicaciones hechas. Este estudio sería mucho más real y rico con los datos que pudiera ofrecer ese censo y comparar si la realidad editorial, se acerca a la realidad de la afición.</p>
  <p>Puedes pedirme mis conclusiones y opiniones personales por redes, pero he preferido excluirlas de este estudio y de esta web.</p>
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