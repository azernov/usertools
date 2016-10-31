<?
/**
 * Снипет заполняет плейсхолдеры с пользовательской информацией
 * В коде страницы плейсхолдеры доступны через префикс ut_user.
 */

if($modx->user->hasSessionContext($modx->context->key) && $modx->user->class_key == 'utUser')
{
    /**
     * @var utUser $utUser
     */
    $utUser = &$modx->user;
    $plsArray = $utUser->toArray();

    $plsArray = array_merge($plsArray,$utUser->Profile->toArray());
    $plsArray = array_merge($plsArray,$utUser->Data->toArray());

    $modx->setPlaceholders($plsArray,'ut_user.');



    //Добавляем инфу в js конфиг
    $keys = array(
        'discount' => $utUser->Data->discount,
    );
    $config = json_encode($keys);
    $script = <<<SCRIPT
<script>
    var utUserConfig = {$config};
</script>
SCRIPT;

    $modx->regClientStartupScript($script,true);

}

return '';