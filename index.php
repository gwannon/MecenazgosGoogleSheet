<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc/inc.php';

$plataformas = ['www.verkami.com', 'www.backerkit.com', 'www.kickstarter.com', 'gamefound.com'];

if(!isset($_REQUEST['log']) || $_REQUEST['log'] != 'no') registerLog();

ob_start();
$file = __DIR__ . '/cache/' . SPREADSHEET_ID . '-' . custom_sanitize_title(SPREADSHEET_SHEET_NAME) . '.html';
if (file_exists($file)) {
    $diff = time() - filectime($file);
    if ($diff <= EXPIRE_CACHE) { //Si es menos de 5 minutos (300 segundos) usamos el cacheo
        echo file_get_contents($file);
        echo "<!-- Cached -->";
        die;
    }
}

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/service_key.json');

$stats = [];

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope('https://www.googleapis.com/auth/spreadsheets');
$service = new Google_Service_Sheets($client);
$sheets = $service->spreadsheets->get(SPREADSHEET_ID, ["ranges" => [SPREADSHEET_SHEET_NAME], "fields" => "sheets"])->getSheets();
$data = $sheets[0]->getData();
$startRow = $data[0]->getStartRow();
$startColumn = $data[0]->getStartColumn();
$rowData = $data[0]->getRowData();
$res = [];
foreach ($rowData as $i => $row) {
    $temp = array();
    $control = 0;
    if (is_array($row->getValues()) && count($row->getValues()) > 0) {
        foreach ($row->getValues() as $j => $value) {
            if (isset($value['formattedValue']) && $value['formattedValue'] != '') {
                $tempObj['formattedValue'] = $value->getFormattedValue();
                $control++;
            } else {
                $tempObj['formattedValue'] = "";
            }
            if ($control > 0) array_push($temp, $tempObj);
        }
    }
    if (count($temp) > 0) array_push($res, $temp);
}

unset($data);
unset($rowData);
?>
<!doctype html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mecenazgos y preventas - Actualizado a <?php echo UPDATE_DATE; ?></title>
    <meta charset="UTF-8" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=VT323&display=swap" rel="stylesheet">
    <link rel="canonical" href="https://gwannon.com/mecenazgos/" />
    <meta name="description" content="Estado de los mecenazgos y preventas de editoriales españolas. Actualizado a <?php echo UPDATE_DATE; ?>.">
    <meta property="og:title" content="Mecenazgos y preventas - Actualizado a <?php echo UPDATE_DATE; ?>">
    <meta property="og:description" content="Estado de los mecenazgos y preventas de editoriales españolas. Actualizado a <?php echo UPDATE_DATE; ?>.">
    <meta property="og:url" content="https://gwannon.com/mecenazgos/" />
</head>

<body>
    <a href="#" class="accesible" title="Contraste ACTIVAR/DESACTIVAR">◐</a>
    <h1>Mecenazgos y preventas</h1>
    <p>Este listado de mecenazgos y preventas está pensado para ayudar a ver el estado de estos y ver si puedes fiarte o no de una editorial a la hora de meter dinero en uno de sus proyectos.</p>
    <p>La fecha de entrega oficial se calcula a usando el último día posible del rango que de la editorial. Por ejemplo, si una editorial dice que la entrega será el tercer trimestre de 2026, la fecha de entrega oficial será el 30/09/2026. La fecha del mecenazgo conseguido es la fecha en que consiguió el objetivo del mecenazgo, no la fecha de finalización del mecenazgo o preventa.</p>
    <p>La fecha de entrega es la fecha de entrega del material físico, a no ser que sea un producto digital, en ese caso, será la fecha de entrega del PDF.</p>
    <p>Aunque legalemente todo son preventas, se considera preventa a las campañas hechas en la propia web de la editorial y mecenazgo a las que se hacen en plataformas de mecenazgo. No se tiene en cuenta si la editorial lo llama de una manera u otra.</p> 
    <p>Estados y su significado:</p>
    <ul>
        <li><b>Retrasado:</b> Se ha entregado más tarde de la fecha oficial de entrega o ha pasado la fecha de entrega oficial y todavía no se ha entregado.</li>
        <li><b>En tiempo:</b> No ha pasado la fecha de entrega oficial o se ha entregado antes de la fecha de entrega oficial.</li>
        <li><b>Pendiente de entregar:</b> No se ha entregado todavía.</li>
        <li><b>Entregado:</b> Ha sido entregado, independientemente de si se ha hecho a tiempo o no.</li>
        <li><b>Entregado a tiempo:</b> Se ha entregado y antes de la fecha oficial de entrega.</li>
    </ul>
    <p>Para que una editorial tenga su propio botón de filtrado debe tener al menos dos mecenazgos o preventas.</p>
    <p>Trataré de actualizarlo quincenalmente e iré modificando la fecha cuando haga actualizaciones.</p>
    <p><b>Última actualización:</b> <?php echo UPDATE_DATE; ?></p>
    <p><b><a href="#stats">Ver datos estadísticos</a></b></p>
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

                if ($proyecto[7]['formattedValue'] == '') $clases[] = 'sinentregar';
                else $clases[] = 'entregado'; ?>
                <div class="element-item <?php echo implode(" ", $clases); ?>">
                    <img src="<?php echo ($image != '' ? $image : "https://dummyimage.com/600x400/000/fff&text=" . urlencode($titulo)); ?>" alt="<?= $titulo ?>" />
                    <h2><a href="<?= $url ?>" target="_blank"><?= $titulo ?></a></h2>
                    <p><?= $editorial ?></p>
                    <p><b><?php echo ($is_preventa ? "Preventa conseguida" : "Mecenazgo conseguido"); ?>:</b> <?php echo $fecha_mecenazgo_conseguido; ?></p>
                    <p><b>Última actualización:</b> <?php echo $fecha_ultima_actualizacion; ?></p>
                    <p><b>Entrega oficial:</b> <span class="oficialdate"><?php echo $fecha_entrega_oficial; ?></span></p>
                    <p><b>Entrega:</b> <span class="date"><?php echo $fecha_final; ?></span></p>
                    <p class="name"><?php echo $sanitize_titulo; ?></p>
                    <p><b>Días de retraso:</b> <span class="days"><?php echo $dias_retraso; ?></span></p>
                </div>

                <?php //Datos editoriales
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
    <h2 id="stats">Datos estadísticos</h2>
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
                    <th>Plataformas de mecenazgo usadas</th>
                </tr>
            </thead>
            <tbody>
                <?php ksort($stats);
                foreach ($stats as $nombre => $editorial) {
                    if ($editorial['proyectos'] > 1) { ?>
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
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
    <p>Las estrellas se otorgan mediante esta fórmula.</p>
    <ul>
        <li>Para empezar hay que tener al menos 5 mecenazgos para poder ser evaluado.</li>
        <li>La primera estrella se consigue, si tienes más mecenazgos entregados que sin entregar. Da igual que estén o no retrasados.</li>
        <li>La segunda estrella se da, si al menos un cuarto los entregados se han entregado a tiempo.</li>
        <li>Se da una estrella, si la media de retraso es menor de 3 meses (90 días).</li>
        <li>Se otorga una estrella, si no hay ningún mecenazgo con un retraso superior a un año (365 días).</li>
        <li>La última estrella se obtiene, si tienes más mecenazgos entregados que sin entregar y al menos la mitad de tus mecenazgos entregados se han entregado a tiempo.</li>
    </ul>
    <!-- <h3>Calendario de entregas</h3> -->
    <p style="border: 1px solid var(--main-color); padding: 5px;">Si detectas datos desactualizados o crees que falta algún mecenazgo o preventa, puedes ponerte en contacto conmigo a través de <a href="mailto:monclus.jorge+mecenazgos@gmail.com">monclus.jorge@gmail.com</a>.</p> 
    <h3>Agradecimientos</h3>
    <ul>
        <li><a href="https://roldelos90.blogspot.com/" target="_blank">Rol de los 90</a> por sus resúmenes anuales de mecenazgos.</li>
    </ul>
    <style>
        <?php echo file_get_contents(__DIR__ . '/inc/style.css'); ?>
    </style>
    <script>
        <?php echo file_get_contents(__DIR__ . '/inc/general.js'); ?>
    </script>
</body>
</html>
<?php $html = ob_get_clean();
file_put_contents($file, $html); //Guardamos en cache
echo $html;