<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc/inc.php';

if(!isset($_REQUEST['log']) || $_REQUEST['log'] != 'no') registerLog();

//if(!isset($_REQUEST['log'])) loadCache();

ob_start();

$stats = [];
$res = accessAuthorSheet(); ?>
<!doctype html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estudio sobre la publicación de autoras de títulos de rol - Actualizado a <?php echo AUTHOR_UPDATE_DATE; ?></title>
    <meta charset="UTF-8" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=VT323&display=swap" rel="stylesheet">
    <link rel="canonical" href="https://gwannon.com/mecenazgos/" />
    <meta name="description" content="Estudio sobre la publicación de autoras de títulos de rol. Actualizado a <?php echo UPDATE_DATE; ?>.">
    <meta property="og:title" content="Estudio sobre la publicación de autoras de títulos de rol - Actualizado a <?php echo AUTHOR_UPDATE_DATE; ?>">
    <meta property="og:description" content="Estudio sobre la publicación de autoras de títulos de rol. Actualizado a <?php echo AUTHOR_UPDATE_DATE; ?>.">
    <meta property="og:url" content="https://gwannon.com/mecenazgos/" />
</head>
<body>
    <a href="#" class="accesible" title="Contraste ACTIVAR/DESACTIVAR">◐</a>
    <h1>Estudio sobre la publicación de autoras de títulos de rol (2026)</h1>
    <p>Este estudio trata de mostrar el papel de las autoras de material rolero en España anualmente, empezando por el año 2026.</p>
    <p>A la hora de elaborar este estudio se han seguido las siguientes reglas y parámetros:</p>
    <ol>
      <li>Se han registrado los productos roleros (juegos, aventuras, suplementos, ensayos teóricos, manuales de técnicas roleras, etc.) escritos por autoras y autores españoles y publicados por editoriales y grupos creativos.</li>
      <li>Son productos publicados físicamente de 16 o más páginas y con PVP. Se busca excluir material de promoción que, por ejemplo, ofrezcan gratis las editoriales en determinadas ocasiones o recompensas de mecenazgos. Para el estudio buscamos que haya una intención de publicar el titulo en sí mismo, no como un producto gratuito, promocional o adicional a otro.</li>
      <li>Se han excluido titulos auto-publicados porque el estudio busca reflejar como se relacionan editoriales/grupos creativos con autoras y en los auto-publicados no se da esa relación.</li>
      <li>Se considera autor aquella persona que la editorial ha promocionado como autor de la publicación. En caso de no quedar claro, se ha considerado autor/es al escritor o escritores principales y se han excluido a personas que han trabajado en su edición, maquetación, corrección,  ilustración y/o promoción.</li>
      <li>Se usa el número de páginas impresas para estimar la importancia que da la editorial a la obra y al autora/autor. No es un sistema perfecto, pero es mucho más realista que simplemente contabilizar el número de obras publicadas. Cuando unas obras tiene varias personas autoras se ha dividido el número de páginas entre el números de autores a la hora de contabilizar cuántas páginas ha escrito cada autora o autor.</li>
      <li>Se ha usado el género atribuido a cada nombre para determinar si estamos ante una autora o un autor. No es un sistema perfecto, pero a falta de pronombres (y su uso en el material promocional) es lo más lejos que se ha podido llegar sin tener que preguntar directamente a les autores e inmiscuirse en la privacidad de las personas. <b>Sentimos no poder representar en este estudio la participación de autores no-binarios/queer.</b></li>
    </ol>
    <p>El estudio es algo vivo que se actualiza según pasan los meses hasta poder tener una idea general del año al terminar este. Si bien hasta finalizado el año, no e tendrán datos definitivos y totale, sí permiten ver tendencias.</p> 
    <p>El estudio puede tener títulos que han sido anunciados como que se publicarán en un año y luego no se cumpla de forma que si al acabar el año o la editorial anunciar otra fecha serán movidos a otro año. Estos cambios son automáticos y quedan registrados de forma que junto a cada estudio vendrá la fecha de la ultima actualización del proyecto.</p>
    <h2><u>Última actualización:</u> <?php echo AUTHOR_UPDATE_DATE; ?></h2>
    <h2>Listado de proyectos roleros publicados de autoras y autores epañoles durante 2026</h2>
    <div class="tables">
      <table>
        <thead>
          <tr>
            <th>Editorial</th>
            <th>Título</th>
            <th>Autoras</th>
            <th>Autores</th>
            <th>Páginas</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($res as $key => $proyecto) { ?>
          <?php if ($key > 0) { ?>
            <tr>
              <td align="left"><?=$proyecto[0]['formattedValue']; ?></td>
              <td align="left"><?=$proyecto[1]['formattedValue']; ?></td>
              <td><?=$proyecto[3]['formattedValue']; ?></td>
              <td><?=$proyecto[4]['formattedValue']; ?></td>
              <td><?=$proyecto[2]['formattedValue']; ?></td>
            </tr>              
          <?php } ?>
        <?php } ?>
      </tbody>
      </table>
    </div>
    
   <?php /*<div class="allcharts">
        <div>
            <h3>Mecenazgos y proyectos</h3>
            <canvas id="pendientes-sin-entregar"></canvas>
        </div>
        <script>
            new Chart(document.getElementById("pendientes-sin-entregar"), {
                type: 'bar',
                data: {
                    labels: ["<?php $temp = []; foreach ($charts as $chart) { $temp[] = $chart['nombre']; } echo implode('", "', $temp); ?>"],
                    datasets: [
                        {
                            label: "Mecenazgos y proyectos entregados a tiempo",
                            data: [<?php $temp = []; foreach ($charts as $chart) { $temp[] = $chart['entregados_a_tiempo']; } echo implode(', ', $temp); ?>]
                        },{
                            label: "Mecenazgos y proyectos entregados con retraso",
                            data: [<?php $temp = []; foreach ($charts as $chart) { $temp[] = $chart['entregados_tarde']; } echo implode(', ', $temp); ?>]
                        },{
                            label: "Mecenazgos y proyectos sin entregar pero en tiempo",
                            data: [<?php $temp = []; foreach ($charts as $chart) { $temp[] = $chart['sin_entregar_pero_a_tiempo']; } echo implode(', ', $temp); ?>]
                        },{
                            label: "Mecenazgos y proyectos sin entregar y retrasados",
                            data: [<?php $temp = []; foreach ($charts as $chart) { $temp[] = $chart['sin_entregar_y_retrasado']; }  echo implode(', ', $temp); ?>]
                        }
                    ]
                },
                options: {
                    scaleShowValues: true,
                    indexAxis: 'y'
                }
            });
        </script> 
    </div> */ ?>
    <h2>Concluiones</h2>
    <p>Sobre las conclusiones, no me considero tan experto en la materia como para sacar unas medianamente válidas. El objetivo es plasmar una realidad y luego dejar a los sujetos de este estudio que saquen sus propias conclusiones.</p>
    <p>Además, debido a que no existe un censo rolero que nos pueda dar la realidad de la afición en cuestiones de genero (tanto de aficionades como de profesionales), solo puedo ofrecer los datos que puedo sacar de las publicaciones hechas. Este estudio sería mucho más real y rico con los datos que pudiera ofrecer ese censo y comprar si la realidad editorial, se acerca a la realidad de la afición.</p>
    <p>Puedes pedirme mis conclusiones y opiniones personales por redes, pero he preferido excluirlas de este estudio y de esta web.</p>
    <h3>Código abierto</h3>
    <p>Todo el código de la web puedes encontrarlo en <a href="https://github.com/gwannon/MecenazgosGoogleSheet" target="_blank">GitHub</a> con licencia GNU General Public License v3.0</a>.</p>
    <style>
        <?php echo file_get_contents(__DIR__ . '/inc/style.css'); ?>
    </style>
    <script>
        <?php echo file_get_contents(__DIR__ . '/inc/general.js'); ?>
    </script>
</body>
</html>
<?php $html = ob_get_clean();
//saveCache($html, $csv);
echo $html;