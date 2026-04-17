<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc/inc.php';

$plataformas = ['www.verkami.com', 'www.backerkit.com', 'www.kickstarter.com', 'gamefound.com', 'crowdfundr.com'];

if(!isset($_REQUEST['log']) || $_REQUEST['log'] != 'no') registerLog();

loadCache();

ob_start();

$stats = [];
$csv = "TÍTULO,EDITORIAL,URL,MECENAZGO CONSEGUIDO,ULTIMA ACTUALIZACION,ENTREGA OFICIAL,ENTREGA FINAL,DIAS DE RETRASO,TIPO\n";
$entiempo = [];
$res = accessSheet(); ?>
<!doctype html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mecenazgos y preventas de juegos de rol en España - Actualizado a <?php echo UPDATE_DATE; ?></title>
    <meta charset="UTF-8" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=VT323&display=swap" rel="stylesheet">
    <link rel="canonical" href="https://gwannon.com/mecenazgos/" />
    <meta name="description" content="Estado de los mecenazgos y preventas de editoriales españolas de juegos de rol. Actualizado a <?php echo UPDATE_DATE; ?>.">
    <meta property="og:title" content="Mecenazgos y preventas de juegos de rol en España - Actualizado a <?php echo UPDATE_DATE; ?>">
    <meta property="og:description" content="Estado de los mecenazgos y preventas de editoriales españolas de juegos de rol. Actualizado a <?php echo UPDATE_DATE; ?>.">
    <meta property="og:url" content="https://gwannon.com/mecenazgos/" />
</head>
<body>
    <a href="#" class="accesible" title="Contraste ACTIVAR/DESACTIVAR">◐</a>
    <h1>Mecenazgos y preventas de juegos de rol</h1>
    <p>Este <b>listado de mecenazgos y preventas de juegos de rol de España</b> está pensado para ayudar a ver el estado de estos y ver si puedes fiarte o no de una editorial a la hora de meter dinero en uno de sus proyectos.</p>
    <p>La fecha de entrega oficial se calcula a usando el último día posible del rango que da la editorial. Por ejemplo, si una editorial dice que la entrega será el tercer trimestre de 2026, la fecha de entrega oficial será el 30/09/2026. La fecha del mecenazgo conseguido es la fecha en que consiguió el objetivo del mecenazgo, no la fecha de finalización del mecenazgo o preventa.</p>
    <p>La fecha de entrega es la fecha de entrega del material físico, a no ser que sea un producto digital, en ese caso, será la fecha de entrega del PDF.</p>
    <p>Aunque legalmente todo son preventas, se considera preventa a las campañas hechas en la propia web de la editorial y mecenazgo a las que se hacen en plataformas de mecenazgo. No se tiene en cuenta si la editorial lo llama de una manera u otra.</p> 
    <p>Para que una editorial tenga su propio botón de filtrado debe tener al menos dos mecenazgos o preventas.</p>
    <p>En las preventas, siempre que ha sido posible, se ha enlazado a la versión de la <b>webs de los proyectos guardadas en archive.org</b> para tener una versión fiable y que las editoriales no puedan editar a su gusto.</p> 
    <p>Si quieres hacer tus propios calculos y estadísticas, puedes bajarte una versión en <a href="/mecenazgos/mecenazgos.csv">formato csv</a>.</p>
    <h2><u>Última actualización:</u> <?php echo UPDATE_DATE; ?></h2>
    <p>Trataré de actualizarlo quincenalmente e iré modificando la fecha cuando haga actualizaciones.</p>
    <hr/>
    <p><a href="#stats" class="button">Ver valoraciones de las editoriales</a> <a href="#enretraso" class="button">Ver mecenazgos que caducan pronto</a> <a href="/mecenazgos/mecenazgos.csv" class="button">Descargar en formato CSV (Excel)</a></p>
    <h2>Estados de los mecenazgos y su significado:</h2>
    <ul>
        <li><b>Retrasado:</b> Se ha entregado más tarde de la fecha oficial de entrega o ha pasado la fecha de entrega oficial y todavía no se ha entregado.</li>
        <li><b>En tiempo:</b> No ha pasado la fecha de entrega oficial o se ha entregado antes de la fecha de entrega oficial.</li>
        <li><b>Pendiente de entregar:</b> No se ha entregado todavía.</li>
        <li><b>Entregado:</b> Ha sido entregado, independientemente de si se ha hecho a tiempo o no.</li>
        <li><b>Entregado a tiempo:</b> Se ha entregado y antes de la fecha oficial de entrega.</li>
    </ul>
    <div id="buttons">
        <div id="filters" class="button-group">
            <h4>Filtrar</h4>
            <button class="button is-checked" data-filter="*">Ver todos</button>
            <button class="button" data-filter=".retrasado">Retrasados</button>
            <button class="button" data-filter=".entiempo">En tiempo</button>
            <button class="button" data-filter=".sinentregar">Pendiente de entregar</button>
            <button class="button" data-filter=".entregado">Entregado</button>
            <button class="button" data-filter=".entregadoatiempo">Entregado a tiempo</button>
            <br />
            <?php $menu = [];
            foreach ($res as $key => $proyecto) {
                $editorial = $proyecto[0]['formattedValue'];
                if (!isset($menu[$editorial])) $menu[$editorial] = 0;
                $menu[$editorial]++;
            }
            ksort($menu);
            foreach ($menu as $key => $items) {
                if ($items >= 2) { ?>
                    <button class="button" data-filter=".<?php echo custom_sanitize_title($key); ?>"><?php echo $key; ?></button>
                <?php }
            } ?>
        </div>
        <div id="sorts" class="button-group">
            <h4>Ordenar por</h4>
            <button class="button is-checked" data-sort-by="name">Nombre</button>
            <button class="button" data-sort-by="days">Dias de retraso ⇩</button>
            <button class="button" data-sort-by="daysasc">Dias de retraso ⇧</button>
            <button class="button" data-sort-by="oficialdate">Fecha de entrega oficial ⇩</button>
            <button class="button" data-sort-by="oficialdateasc">Fecha de entrega oficial ⇧</button>
        </div>
    </div>
    <div class="grid">
        <?php foreach ($res as $key => $proyecto) {
            if ($key > 0) {
                $clases = [];

                //Datos
                $editorial = $proyecto[0]['formattedValue'];
                $clases[] = custom_sanitize_title($editorial);
                $titulo = $proyecto[1]['formattedValue'];
                $sanitize_titulo = custom_sanitize_title($titulo);
                $url = $proyecto[2]['formattedValue'];
                $parse = parse_url($url);
                $plataforma = $parse['host'];
                $image = $proyecto[3]['formattedValue'];

                if(in_array($plataforma, $plataformas)) $is_preventa = false;
                else $is_preventa = true;

                //Fechas
                $fecha_mecenazgo_conseguido = transformDate($proyecto[4]['formattedValue']);
                $fecha_ultima_actualizacion = transformDate($proyecto[5]['formattedValue']);
                $fecha_entrega_oficial = transformDate($proyecto[6]['formattedValue']);
                $fecha_final = (isset($proyecto[7]['formattedValue']) && $proyecto[7]['formattedValue'] != '' ? transformDate($proyecto[7]['formattedValue']) : "PENDIENTE");

                //Pasada la fecha de entrega oficial
                if ($fecha_final != "PENDIENTE") $ahora = new DateTime($fecha_final);
                else $ahora = new DateTime("now");
                $entrega = new DateTime($fecha_entrega_oficial);
                if ($entrega < $ahora) {
                    $clases[] = "retrasado";
                    $interval = $entrega->diff($ahora);
                    $dias_retraso = $interval->days;
                } else {
                    $clases[] = "entiempo";
                    $dias_retraso = 0;
                    if (isset($proyecto[7]['formattedValue']) && $proyecto[7]['formattedValue'] != '') $clases[] = "entregadoatiempo";
                }

                $ahora = new DateTime("now");
                if($entrega > $ahora && $fecha_final == 'PENDIENTE') {
                    $entiempo[] = [
                      "fecha" => $entrega,
                      "titulo" => $titulo,
                      "url" => $url,
                      "editorial" => $editorial
                    ];
                }

                if ($proyecto[7]['formattedValue'] == '') $clases[] = 'sinentregar';
                else $clases[] = 'entregado'; ?>
                <div class="element-item <?php echo implode(" ", $clases); ?>">
                    <img src="<?php echo ($image != '' ? $image : "https://dummyimage.com/600x400/000/fff&text=" . urlencode($titulo)); ?>" alt="<?= $titulo ?>" />
                    <h2><a href="<?= $url ?>" target="_blank"><?= $titulo ?></a></h2>
                    <h3><?= $editorial ?></h3>
                    <p><b><?php echo ($is_preventa ? "Preventa conseguida" : "Mecenazgo conseguido"); ?>:</b> <?php echo $fecha_mecenazgo_conseguido; ?></p>
                    <p><b>Última actualización:</b> <?php echo $fecha_ultima_actualizacion; ?></p>
                    <p><b>Entrega oficial:</b> <span class="oficialdate"><?php echo $fecha_entrega_oficial; ?></span></p>
                    <p><b>Entrega:</b> <span class="date"><?php echo $fecha_final; ?></span></p>
                    <p class="name"><?php echo $sanitize_titulo; ?></p>
                    <p><b>Días de retraso:</b> <span class="days"><?php echo $dias_retraso; ?></span></p>
                </div>
                <?php 
                
                $csv .= '"'.addslashes($titulo).'",'.
                '"'.addslashes($editorial).'",'.
                '"'.$url.'",'.
                '"'.$fecha_mecenazgo_conseguido.'",'.
                '"'.$fecha_ultima_actualizacion.'",'.
                '"'.$fecha_entrega_oficial.'",'.
                '"'.$fecha_final.'",'.
                '"'.$dias_retraso.'",'.
                '"'.($is_preventa ? "Preventa" : "Mecenazgo").'",'.
                "\n";
                
                //Datos editoriales
                if (!isset($stats[$editorial])) {
                    $stats[$editorial] = [
                        'proyectos' => 0,
                        'sin_entregar' => 0,
                        'entregados' => 0,
                        'entregados_a_tiempo' => 0,
                        'entregados_tarde' => 0,
                        'dias_retraso' => 0,
                        'sin_entregar_pero_a_tiempo' => 0,
                        'max_retraso' => 0,
                        'plataformas' => [],
                    ];
                }
                $stats[$editorial]['proyectos']++;
                if (in_array('sinentregar', $clases)) $stats[$editorial]['sin_entregar']++;
                if (in_array('entregado', $clases)) $stats[$editorial]['entregados']++;
                if (in_array('entregadoatiempo', $clases)) $stats[$editorial]['entregados_a_tiempo']++;
                if (in_array('entregado', $clases) && in_array('retrasado', $clases)) $stats[$editorial]['entregados_tarde']++;
                if (in_array('sinentregar', $clases) && in_array('entiempo', $clases)) $stats[$editorial]['sin_entregar_pero_a_tiempo']++;
                $stats[$editorial]['dias_retraso'] = $stats[$editorial]['dias_retraso'] + $dias_retraso;
                if ($dias_retraso > $stats[$editorial]['max_retraso']) $stats[$editorial]['max_retraso'] = $dias_retraso;
                if(!in_array($plataforma, $stats[$editorial]['plataformas']) && in_array($plataforma, $plataformas)) $stats[$editorial]['plataformas'][] = $plataforma;
            }
        } ?>
    </div>
    <h2 id="stats">Valoración de editoriales de juegos de rol al hacer mecenazgos y preventas</h2>
    <div>
        <table>
            <thead>
                <tr>
                    <th>Editorial</th>
                    <th>Estrellas</th>
                    <th>Nº proyectos</th>
                    <th>Proyectos sin entregar</th>
                    <th>Proyectos sin entregar,<br />pero aun en tiempo</th>
                    <th>Proyectos entregados</th>
                    <th>Proyectos entregados a tiempo</th>
                    <th>Proyectos entregados tarde</th>
                    <th>Días de retraso medio</th>
                    <th>Acumulado de días de retraso</th>
                    <th>Máximo días de retraso</th>
                    <th>Nª de plataformas de mecenazgo usadas</th>
                </tr>
            </thead>
            <tbody>
                <?php ksort($stats);

                $csv .="\n\nEDITORIAL,ESTRELLAS,Nº PROYECTOS,PROYECTOS SIN ENTREGAR,\"PROYECTOS SIN ENTREGAR, PERO AUN EN TIEMPO\",PROYECTOS ENTREGADOS,PROYECTOS ENTREGADOS A TIEMPO,PROYECTOS ENTREGADOS TARDE,DÍAS DE RETRASO MEDIO,ACUMULADO DE DÍAS DE RETRASO,MÁXIMO DÍAS DE RETRASO,Nª DE PLATAFORMAS DE MECENAZGO USADAS\n";

                foreach ($stats as $nombre => $editorial) { if ($editorial['proyectos'] > 1) { ?>
                    <tr>
                        <th><?php echo $nombre; ?></th>
                        <td><span class="stars-<?php $stars = getStars($editorial); echo $stars; ?>"><?php echo $stars; ?> estrellas</span></td>
                        <td><?php echo $editorial['proyectos']; ?></td>
                        <td><?php echo $editorial['sin_entregar']; ?></td>
                        <td><?php echo $editorial['sin_entregar_pero_a_tiempo']; ?></td>
                        <td><?php echo $editorial['entregados']; ?></td>
                        <td><?php echo $editorial['entregados_a_tiempo']; ?></td>
                        <td><?php echo $editorial['entregados_tarde']; ?></td>
                        <td><?php echo floor(($editorial['dias_retraso'] / $editorial['proyectos'])); ?></td>
                        <td><?php echo $editorial['dias_retraso']; ?></td>
                        <td><?php echo $editorial['max_retraso']; ?></td>
                        <td><?php echo count($editorial['plataformas']); ?><!-- <?php echo implode(", ", $editorial['plataformas']); ?> --></td>
                    </tr>
                    <?php 
                    
                        $csv .= '"'.addslashes($nombre).'",'.
                        $stars.','.
                        $editorial['proyectos'].','.
                        $editorial['sin_entregar'].','.
                        $editorial['sin_entregar_pero_a_tiempo'].','.
                        $editorial['entregados'].','.
                        $editorial['entregados_a_tiempo'].','.
                        $editorial['entregados_tarde'].','.
                        floor(($editorial['dias_retraso'] / $editorial['proyectos'])).','.
                        $editorial['dias_retraso'].','.
                        $editorial['max_retraso'].','.
                        count($editorial['plataformas'])."\n";
                    
                    
                    } } ?>
            </tbody>
        </table>
    </div>
    <p>Las <b>estrellas de las editoriales de juegos de rol</b> se otorgan mediante la siguiente fórmula.</p>
    <ul>
        <li>Para empezar hay que <b>tener al menos 5 mecenazgos</b> para poder ser evaluado.</li>
        <li>La primera estrella se consigue, si tienes <b>más mecenazgos entregados que sin entregar</b>. Da igual que estén o no retrasados.</li>
        <li>La segunda estrella se da, si al menos <b>un cuarto los entregados se han entregado a tiempo</b>.</li>
        <li>Se da una estrella, si la <b>media de retraso es menor de 3 meses (90 días)</b>.</li>
        <li>Se otorga una estrella, si no hay <b>ningún mecenazgo con un retraso superior a un año (365 días)</b>.</li>
        <li>La última estrella se obtiene, si tienes <b>más mecenazgos entregados que sin entregar y al menos la mitad de tus mecenazgos entregados se han entregado a tiempo</b>.</li>
    </ul>
    <h2 id="enretraso">Mecenazgos y preventas que entran en retraso próximamente</h2>
    <div>
        <table>
        <thead>
            <tr>
            <th>Producto rolero</th>
            <th>Editorial de juegos de rol</th>
            <th>Fecha de entrega del mecenazgo</th>
            <th>Dias hasta entrar en retraso</th>
            </tr>
        </thead>
        <tbody>
            <?php $ahora = new DateTime("now"); usort($entiempo, 'sortByOrder'); foreach ($entiempo as $retraso) { ?>
                <tr>
                    <td style="text-align: left;"><a href="<?php echo $retraso['url']; ?>"><?php echo $retraso['titulo']; ?></a></td>
                    <td><?php echo $retraso['editorial']; ?></td>
                    <td><?php echo $retraso['fecha']->format('Y/m/d'); ?></td>
                    <td><?php $interval = $retraso['fecha']->diff($ahora); echo $interval->days; ?></td>
                </tr>
            <?php } ?>
        </tbody>
        </table>
    </div>
    <p style="border: 1px solid var(--main-color); padding: 5px;">Si detectas datos desactualizados o crees que falta algún mecenazgo o preventa, puedes ponerte en contacto conmigo a través de <a href="mailto:monclus.jorge+mecenazgos@gmail.com">monclus.jorge@gmail.com</a>. Con una dirección web donde se vea el mecenazgo/preventa y la fecha de entrega oficial y la de entrega final (si la hay) me valdría.</p> 
    <h3>Opiniones, críticas y comentarios sobre la herramienta de mecenazgos y preventas</h3>
    <p>Si quieres saber que opina el público sobre esta herramienta podéis abrir alguno de estos enlaces. Voy a poner de todos los que encuentre.</p>
    <ul>
        <li>2026/04/02 - <a href="https://bsky.app/profile/gwannon.com/post/3miipzx43hc2w">Bluit original de Gwannon (o sea yo)</a></li> 
        <li>2026/04/09 - <a href="https://roleplus.app/publicaciones/mecenazgos-y-preventas">Crowker en Role+</a></li>
        <li>2026/04/14 - <a href="https://piedrapapeld20.com/09-en-el-cruce-no-gires-a-la-derecha/">PiedraPapelD20 en su blog</a></li>
        <li>2026/04/16 - <a href="https://www.youtube.com/live/_UTBeN8Lpp0?t=2258s">Turbiales en su canal de Youtube</a></li>
    </ul>
    <p>Si encontráis más opiniones y críticas podéis usar el email anterior para pasármelas y que pueda ponerlas aquí.</p>
    <h3>Agradecimientos</h3>
    <ul>
        <li><a href="https://roldelos90.blogspot.com/" target="_blank">Rol de los 90</a> por sus resúmenes anuales de mecenazgos.</li>
        <li><a href="https://web.archive.org/web/20230326212232/https://roltrasos.info/preventas" target="_blank">Roltrasos</a> por la información que he podido constratar con su web.</li>
        <li><a href="https://web.archive.org/" target="_blank">archive.org</a> por guardar copias de la webs de las preventas de las editoriales y poder consultar fechas aunque las hayan borrado o editado.</a>
    </ul>
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
saveCache($html, $csv);
echo $html;