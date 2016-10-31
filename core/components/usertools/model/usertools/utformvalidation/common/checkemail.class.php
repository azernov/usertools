<?
require_once MODX_CORE_PATH . 'components/usertools/model/usertools/utformvalidation/utformvalidationrule.class.php';

/**
 * Проверка email на корректность
 * Class checkEmail
 */
class checkEmail extends utFormValidationRule{
    protected $defaultErrorMessage = 'Укажите корректный email';

    public function isValid($value)
    {
        $result = true;
        if(!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/ui',$value))
        {
            $this->validator->addMessage($this->fieldName,'invalid',$this->getErrorMessage());
            $result = false;
        }

        return $result;
    }
}