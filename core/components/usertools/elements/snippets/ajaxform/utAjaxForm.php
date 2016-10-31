<?php
/** @var array $scriptProperties */
/** @var utAjaxForm $utAjaxForm */

$utAjaxForm = $modx->getService('utajaxform', 'utAjaxForm', MODX_CORE_PATH . 'components/usertools/model/usertools/ajaxform/', $scriptProperties);

/* @var utApplication $usertools */
$usertools = $modx->getService('usertools', 'utApplication', MODX_CORE_PATH . 'components/usertools/model/usertools/');

if (!$utAjaxForm instanceof utAjaxForm) return '';

$utAjaxForm->initialize($modx->context->key);

$snippet = $modx->getOption('snippet', $scriptProperties, 'FormIt', true);
$tpl = $modx->getOption('form', $scriptProperties, 'tpl.utAjaxForm.example', true);
$formBySnippet = $modx->getOption('formBySnippet', $scriptProperties, false, true);
$formSelector = $modx->getOption('formSelector', $scriptProperties, 'ajax_form', true);
if (!isset($placeholderPrefix))
{
    $placeholderPrefix = 'fi.';
}

if ($formBySnippet)
{
    $content = $modx->runSnippet($tpl, $scriptProperties);
}
else
{
    $content = $modx->getChunk($tpl, $scriptProperties);
}


/** @var modChunk $chunk */
if (!$content)
{
    return $modx->lexicon('af_err_chunk_nf', array('name' => $tpl));
}

// Add selector to tag form
if (preg_match('/<form.*?class="(.*?)"/', $content, $matches))
{
    $classes = explode(' ', $matches[1]);
    if (!in_array($formSelector, $classes))
    {
        $classes[] = $formSelector;
        $classes = str_replace('class="' . $matches[1] . '"', 'class="' . implode(' ', $classes) . '"', $matches[0]);
        $content = str_replace($matches[0], $classes, $content);
    }
}
else
{
    $content = str_replace('<form', '<form class="' . $formSelector . '"', $content);
}

// Add method = post
if (preg_match('/<form.*?method="(.*?)"/', $content))
{
    $content = preg_replace('/<form(.*?)method="(.*?)"/', '<form\\1method="post"', $content);
}
else
{
    $content = str_replace('<form', '<form method="post"', $content);
}

// Add action for form processing
$hash = md5(http_build_query($scriptProperties));

$action = '<input type="hidden" name="af_action" value="' . $hash . '" />';
if ((strpos($content, '</form>') !== false))
{
    if (preg_match('/<input.*?name="af_action".*?>/', $content, $matches))
    {
        $content = str_replace($matches[0], '', $content);
    }
    $content = str_replace('</form>', "\n\t$action\n</form>", $content);
}

// Save settings to user`s session
$_SESSION['utAjaxForm'][$hash] = $scriptProperties;

// Call snippet for preparation of form
$action = !empty($_REQUEST['af_action'])
    ? $_REQUEST['af_action']
    : $hash;

//$utAjaxForm->process($action, $_REQUEST);

// Return chunk
return $content;