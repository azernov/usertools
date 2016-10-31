<?
/**
 * Выполнение деавторизации пользователя
 * @var $listenerMode - если поставить 1, то будет ждать ut_action=logout в $_REQUEST
 */

$listenerMode = $modx->getOption("listenerMode",$scriptProperties,false);

//Если пользователь не залогинен - просто выходим
if(!$modx->user->hasSessionContext($modx->context->key) || $modx->user->class_key != 'utUser') return '';

/* @var utApplication $usertools */
$usertools = $modx->getService('usertools', 'utApplication', MODX_CORE_PATH . 'components/usertools/model/usertools/');
$userHandler = $usertools->getUserHandler();

//Если мы в слушающем режиме и к нам пришел флаг выхода, либо в обычном режиме - сразу разлогиниваем
if(($listenerMode && isset($_REQUEST['ut_action']) && $_REQUEST['ut_action'] == 'logout') || !$listenerMode){
    $result = $userHandler->logout();
    $modx->sendRedirect($result->options['redirect_url']);
    exit;
}

return '';