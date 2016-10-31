<?
/**
 * Снипет делает проверку, залогинен ли пользователь (именно пользователь-дилер)
 * @var array $scriptProperties
 */

$thenTpl = $modx->getOption('thenTpl',$scriptProperties,false);
$elseTpl = $modx->getOption('elseTpl',$scriptProperties,false);

/* @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');

/**
 * @var utApplication $usertools
 */
$usertools = $modx->getService('usertools', 'utApplication', MODX_CORE_PATH . 'components/usertools/model/usertools/');

if($modx->user->hasSessionContext($modx->context->key) && $modx->user->class_key == 'utUser')
{
    return $thenTpl ? $pdoFetch->getChunk($thenTpl) : '';
}
else
{
    return $elseTpl ? $pdoFetch->getChunk($elseTpl) : '';
}