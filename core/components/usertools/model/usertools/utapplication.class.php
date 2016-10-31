<?
require_once MODX_CORE_PATH.'components/usertools/model/usertools/utoperationresult.class.php';

/**
 * Class utApplication
 * Главный класс приложения ut
 */
class utApplication{
    /**
     * @var modX
     */
    protected $modx;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var utEvent
     */
    protected $_utEvent = null;

    /**
     * @var utUserHandler
     */
    protected $_utUserHandler = null;

    public function __construct(&$modx, $config = array())
    {
        $this->modx = &$modx;
        $this->modx->addPackage('usertools', MODX_CORE_PATH . 'components/usertools/model/');
        $this->loadLexicons();
        if (!MODX_API_MODE)
        {
            $this->loadJavaScriptConfig();
            $this->loadJavaScripts();
            $this->setPlaceholders();
        }
        $this->config = array_merge($this->config,$config);
    }

    /**
     * Позволяет обращаться к методу типа getBlabla() как к свойству $blabla
     * @param $property
     * @return null
     */
    public function __get($property)
    {
        $methodName = 'get' . ucfirst($property);
        if (method_exists($this, $methodName))
        {
            return $this->$methodName();
        }
        return null;
    }

    public function loadLexicons()
    {
        $this->modx->lexicon->load('usertools:default');
    }

    /**
     * Подключает необходимые java-скрипты при выполнении
     */
    public function loadJavaScripts()
    {
        //Если это страница заказа, то подгрузим еще скрипт для работы со страницей
        if($this->modx->resource->id == $this->modx->getOption('ut_order_page'))
        {
            //$this->modx->regClientScript(MODX_ASSETS_URL.'components/usertools/order/js/order.js');
        }
    }

    /**
     * Добавляет javascript объект на страницу с конфигурационными параметрами, указанными в $keys
     * @param array $keys
     * @return array
     */
    protected function generateJavaScriptConfig($keys = array())
    {
        $config = array();
        foreach ($keys as $key)
        {
            $config[$key] = $this->modx->getOption($key);
        }

        $config['ut_order_history_page_url'] = $this->modx->makeUrl($this->modx->getOption('ut_order_history_page'));
        return $config;
    }

    public function loadJavaScriptConfig()
    {
        $keys = array(
            //TODO перечислить только те поля конфига, которые нужны на frontend
            'ut_login_page',
        );
        $config = $this->generateJavaScriptConfig($keys);
        $config = json_encode($config);
        $script = <<<SCRIPT
<script>
    var utSiteConfig = {$config};
</script>
SCRIPT;

        $this->modx->regClientStartupScript($script,true);
    }

    public function setPlaceholders()
    {
        $contextKey = $this->modx->context->key;
        $this->modx->setPlaceholder('context_key',$contextKey);
    }

    /**
     * Возвращает событийный объект
     * @return null|utEvent
     */
    public function getUtEvent()
    {
        if ($this->_utEvent === null)
        {
            require_once dirname(__FILE__) . '/utevent.class.php';
            $this->_utEvent = new utEvent($this->modx, $this);
        }
        return $this->_utEvent;
    }

    /**
     * Возвращает обработчик пользователя
     * @return null|utUserHandler
     */
    public function getUserHandler()
    {
        if ($this->_utUserHandler === null)
        {
            require_once dirname(__FILE__) . '/utuserhandler.class.php';
            $this->_utUserHandler = new utUserHandler($this->modx, $this);
        }
        return $this->_utUserHandler;
    }

    /**
     * Отправка почты
     *
     * @param string $email
     * @param string $subject
     * @param string $body
     *
     * @return void
     */
    public function sendEmail($email, $subject, $body = 'no body set') {
        $emails = explode(',',$email);
        if (!isset($this->modx->mail) || !is_object($this->modx->mail)) {
            $this->modx->getService('mail', 'mail.modPHPMailer');
        }
        foreach($emails as $oneEmail)
        {
            $oneEmail = trim($oneEmail);
            $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
            $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
            $this->modx->mail->setHTML(true);
            $this->modx->mail->set(modMail::MAIL_SUBJECT, trim($subject));
            $this->modx->mail->set(modMail::MAIL_BODY, $body);
            $this->modx->mail->set(modMail::MAIL_BODY_TEXT, strip_tags($body));
            $this->modx->mail->address('to', trim($oneEmail));
            if (!$this->modx->mail->send()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$this->modx->mail->mailer->ErrorInfo);
            }
            $this->modx->mail->reset();
        }
    }
}