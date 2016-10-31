<?
/**
 * Снипет делает проверку, залогинен ли пользователь и делает редирект, в зависимости от параметров
 * @var array $scriptProperties
 */

$then = $modx->getOption('then',$scriptProperties,false);
$else= $modx->getOption('else',$scriptProperties,false);

/**
 * @var utApplication $usertools
 */
$usertools = $modx->getService('usertools', 'utApplication', MODX_CORE_PATH . 'components/usertools/model/usertools/');


if($modx->user->hasSessionContext($modx->context->key) && $modx->user->class_key == 'utUser')
{
    if($then)
    {
        $modx->sendRedirect($modx->makeUrl($then));
        exit;
    }
}
else
{
    if($else)
    {
        $modx->sendRedirect($modx->makeUrl($else));
        exit;
    }
}

return '';