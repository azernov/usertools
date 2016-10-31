<?

/**
 * Класс валидетора форм, не связанных напрямую с моделями данных в БД
 * Class utFormValidator
 */
class utFormValidator {
    protected $messages = array();
    protected $rules = array();
    protected $fieldsErrorMessages = array();

    protected $values = array();

    protected $options = array();

    /**
     * @var modX
     */
    protected $modx;

    public $config = array();

    /**
     * @param modX $modx
     * @param array $config
     */
    public function __construct(&$modx, $config = array())
    {
        $this->modx = &$modx;
        $this->config = $config;
    }

    public function resetMessages()
    {
        $this->messages = array();
    }

    /**
     * Задать значения и правила валидации
     * @param array $values
     * @param array $rules - ассоциативный массив. Ключ - название поля, значение - строка или массив с названиями правил валидации
     * @param array $fieldsErrorMessages - ассоциативный массив. Ключ - название поля, значение - строка для ошибки в этом поле
     */
    public function initialize($values, $rules, $fieldsErrorMessages = array())
    {
        $this->rules = $rules;
        $this->values = $values;
        $this->fieldsErrorMessages = $fieldsErrorMessages;

        $this->resetMessages();
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function validate()
    {
        foreach($this->rules as $field => $rule)
        {
            $this->runRule($field,$rule);
        }
        return !$this->hasMessages();
    }

    protected function runRule($field,$rule)
    {
        if(empty($rule))
        {
            return;
        }
        if(is_array($rule))
        {
            foreach($rule as $ruleString)
            {
                $this->runRule($field,$ruleString);
            }
        }
        else
        {
            $basePath = MODX_CORE_PATH.'components/usertools/model/usertools/utformvalidation/';
            $pieces = explode('.',$rule);
            $className = array_pop($pieces);
            $fullPath = $basePath.implode('/',$pieces).'/';
            $fileName = $fullPath.strtolower($className).'.class.php';
            if(file_exists($fileName))
            {
                require_once $fileName;
                if(class_exists($className))
                {
                    /**
                     * @var utFormValidationRule $rule
                     */
                    $rule = new $className($this->modx,$this,$field);
                    if(isset($this->fieldsErrorMessages[$field]))
                    {
                        $rule->setErrorMessage($this->fieldsErrorMessages[$field]);
                    }
                    $rule->isValid($this->values[$field]);
                }
                else
                {
                    $this->modx->log(MODX_LOG_LEVEL_ERROR,'Не найден класс для валидации '.$className.' в файле '.$fileName);
                }
            }
            else
            {
                $this->modx->log(MODX_LOG_LEVEL_ERROR,'Не найден файл для валидации '.$fileName);
            }
        }
    }

    public function addMessage($field,$name = 'invalid',$message = 'invalid')
    {
        array_push($this->messages,array(
            'field' => $field,
            'name' => $name,
            'message' => $message,
        ));
    }

    public function hasMessages()
    {
        return (count($this->messages) > 0);
    }
}