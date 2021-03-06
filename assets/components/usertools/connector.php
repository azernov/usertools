<?php
/**
 * Cs Connector
 *
 * @package carolesmokes
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = MODX_CORE_PATH.'components/carolesmokes/';

/* handle request */
$path = $corePath.'processors/';
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));