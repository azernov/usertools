<?

/**
 * Класс валидации одного поля формы.
 * Используется для валидации форм, не связанных с моделью, хранящейся в БД
 * Class utFormValidationRule
 */
class utFormValidationRule{
    /**
     * @var utFormValidator
     */
    protected $validator;

    protected $fieldName;

    protected $modx;

    protected $defaultErrorMessage = 'Укажите корректное значение';
    protected $errorMessage = '';

    /**
     * @param modX $modx
     * @param utFormValidator $validator
     * @param string $fieldName
     */
    public function __construct(&$modx, &$validator,$fieldName)
    {
        $this->modx = &$modx;
        $this->validator = &$validator;
        $this->fieldName = $fieldName;
    }

    public function setErrorMessage($message){
        $this->errorMessage = $message;
    }

    public function getErrorMessage(){
        return empty($this->errorMessage) ? $this->defaultErrorMessage : $this->errorMessage;
    }

    public function isValid($value)
    {
        return true;
    }
}