<?php
/**
 * Class utAjaxForm
 * Класс для обработки форм через ajax (слегка переписанный компонент от Василия Безумкина)
 * @package ut
 * @subpackage ajaxform
 */
class utAjaxForm {
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config;
    /** @var array $initialized */
    public $initialized = array();


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;

        $corePath = MODX_CORE_PATH . 'components/usertools/model/ajaxform/';
        $assetsPath = MODX_ASSETS_PATH . 'components/usertools/ajaxform/';
        $assetsUrl = MODX_ASSETS_URL . 'components/usertools/ajaxform/';

        $this->modx->lexicon->load('usertools:ajaxformdefault');

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'actionUrl' => $assetsUrl.'action.php',

            'formSelector' => 'ajax_form',
            'closeMessage' => $this->modx->lexicon('af_message_close_all'),
            'json_response' => true,

            'corePath' => $corePath,
            'assetsPath' => $assetsPath,

            'frontend_css' => '[[+assetsUrl]]css/default.css',
            'frontend_js' => '[[+assetsUrl]]js/default.js',
        ), $config);
    }


    /**
     * Initializes utAjaxForm into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array $scriptProperties array with additional parameters
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $scriptProperties = array()) {
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;
        if (!empty($this->initialized[$ctx])) {
            return true;
        }
        switch ($ctx) {
            case 'mgr': break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    if ($uts = trim($this->config['frontend_css'])) {
                        if (preg_match('/\.css/i', $uts)) {
                            $this->modx->regClientCSS(str_replace('[[+assetsUrl]]', $this->config['assetsUrl'], $uts));
                        }
                    }

                    $config_js = preg_replace(array('/^\n/', '/\t{6}/'), '', '
						afConfig = {
							assetsUrl: "'.$this->config['assetsUrl'].'"
							,actionUrl: "'.str_replace('[[+assetsUrl]]', $this->config['assetsUrl'], $this->config['actionUrl']).'"
							,closeMessage: "'.$this->config['closeMessage'].'"
							,formSelector: "form.'.$this->config['formSelector'].'"
						};
					');
                    if (file_put_contents($this->config['assetsPath'] . 'js/config.js', $config_js)) {
                        $this->modx->regClientStartupScript($this->config['assetsUrl'] . 'js/config.js');
                    }
                    else {
                        $this->modx->regClientStartupScript("<script type=\"text/javascript\">\n".$config_js."\n</script>", true);
                    }

                    if ($js = trim($this->config['frontend_js'])) {
                        if (preg_match('/\.js/i', $js)) {
                            $script = <<<EOD
								<script type="text/javascript">
									if(typeof jQuery == "undefined") {
										document.write("<script src=\"{$this->config['assetsUrl']}js/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
									}
								</script>
EOD;

                            $this->modx->regClientScript(preg_replace(array('/^\n/', '/\t{7}/'), '', $script), true);
                            $this->modx->regClientScript(str_replace('[[+assetsUrl]]', $this->config['assetsUrl'], $js));
                        }
                    }
                }
                $this->initialized[$ctx] = true;
                break;
        }
        return true;
    }


    /**
     * Loads snippet for form processing
     *
     * @param $action
     * @param array $fields
     *
     * @return array|string
     */
    public function process($action, array $fields = array()) {
        if (!isset($_SESSION['utAjaxForm'][$action])) {
            return $this->error('af_err_action_nf');
        }
        unset($fields['af_action'], $_POST['af_action']);

        $scriptProperties = $_SESSION['utAjaxForm'][$action];
        $scriptProperties['fields'] = $fields;
        $scriptProperties['utAjaxForm'] = $this;

        $name = $scriptProperties['snippet'];
        $set = '';
        if (strpos($name, '@') !== false) {
            list($name, $set) = explode('@', $name);
        }



        /** @var modSnippet $snippet */
        if ($snippet = $this->modx->getObject('modSnippet', array('name' => $name))) {
            $properties = $snippet->getProperties();
            $property_set = !empty($set)
                ? $snippet->getPropertySet($set)
                : array();

            $scriptProperties = array_merge($properties, $property_set, $scriptProperties);
            $snippet->_cacheable = false;
            $snippet->_processed = false;



            /*foreach($scriptProperties as $key => $value)
            {
                if(empty($value))
                {
                    unset($scriptProperties[$key]);
                }
            }*/
            /*ob_start();
            var_dump($scriptProperties);
            $out = ob_get_clean();
            $this->modx->log(MODX_LOG_LEVEL_ERROR,$out);*/

            $response = $snippet->process($scriptProperties);





            if (strtolower($snippet->name) == 'formit') {
                $response = $this->handleFormIt($scriptProperties);
            }

            return $response;
        }
        else {
            return $this->error('af_err_snippet_nf', array(), array('name' => $name));
        }
    }

    /**
     * МЕТОД ДОРАБОТАН МНОЙ!
     * @param $fields
     * @param string $parent  добавлен этот параметр
     * @param $prefix
     * @param $errors
     */
    public function processErrors($fields, $parent = '', $prefix, &$errors)
    {
        $parent = $parent == '' ? '' : $parent.'.';

        foreach ($fields as $k => $v) {
            if(is_array($v))
            {
                $this->processErrors($v,$parent.$k,$prefix,$errors);
            }

            if (isset($this->modx->placeholders[$prefix.'error.'.$parent.$k])) {
                $errors[$parent.$k] = $this->modx->placeholders[$prefix.'error.'.$parent.$k];
            }
        }
    }


    /**
     * Method for obtaining data from FormIt
     *
     * @param array $scriptProperties
     *
     * @return array|string
     */
    public function handleFormIt(array $scriptProperties = array()) {
        $plPrefix = isset($scriptProperties['placeholderPrefix'])
            ? $scriptProperties['placeholderPrefix']
            : 'fi.';

        $errors = array();
        $this->processErrors($scriptProperties['fields'],'',$plPrefix, $errors);
        /*foreach ($scriptProperties['fields'] as $k => $v) {
            if (isset($this->modx->placeholders[$plPrefix.'error.'.$k])) {
                $errors[$k] = $this->modx->placeholders[$plPrefix.'error.'.$k];
            }
        }*/

        if (!empty($errors)) {
            $message = !empty($this->modx->placeholders[$plPrefix.'validation_error_message'])
                ? $this->modx->placeholders[$plPrefix.'validation_error_message']
                : 'af_err_has_errors';
            $status = 'error';
        }
        else {
            $message = isset($this->modx->placeholders[$plPrefix.'successMessage'])
                ? $this->modx->placeholders[$plPrefix.'successMessage']
                : 'af_success_submit';
            $status = 'success';
        }

        return $this->$status($message, $errors);
    }


    /**
     * This method returns an error of the order
     *
     * @param string $message A lexicon key for error message
     * @param array $data.Additional data, for example cart status
     * @param array $placeholders Array with placeholders for lexicon entry
     *
     * @return array|string $response
     */
    public function error($message = '', $data = array(), $placeholders = array()) {
        $response = array(
            'success' => false,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        );

        return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
    }


    /**
     * This method returns an success of the order
     *
     * @param string $message A lexicon key for success message
     * @param array $data.Additional data, for example cart status
     * @param array $placeholders Array with placeholders for lexicon entry
     *
     * @return array|string $response
     */
    public function success($message = '', $data = array(), $placeholders = array()) {
        $response = array(
            'success' => true,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data' => $data,
        );

        return $this->config['json_response'] ? $this->modx->toJSON($response) : $response;
    }
}
