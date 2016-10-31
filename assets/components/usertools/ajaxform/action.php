<?php

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var utAjaxForm $utAjaxForm */
$utAjaxForm = $modx->getService('utajaxform','utAjaxForm',MODX_CORE_PATH.'components/carolesmokes/model/carolesmokes/ajaxform/', array());

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
	$modx->sendRedirect($modx->makeUrl($modx->getOption('site_start'),'','','full'));
}
elseif (empty($_REQUEST['af_action'])) {
	echo $utAjaxForm->error('af_err_action_ns');
}
else {
	echo $utAjaxForm->process($_REQUEST['af_action'], $_REQUEST);
}

@session_write_close();