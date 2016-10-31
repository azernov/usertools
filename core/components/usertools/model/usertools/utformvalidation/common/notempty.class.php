<?
require_once MODX_CORE_PATH . 'components/usertools/model/usertools/utformvalidation/utformvalidationrule.class.php';

/**
 * Проверка на непустое значение
 * Class checkName
 */
class notEmpty extends utFormValidationRule{
    protected $defaultErrorMessage = 'Поле не может быть пустым';

    public function isValid($value)
    {
        $result = true;
        if(trim($value) == '')
        {
            $this->validator->addMessage($this->fieldName,'invalid',$this->getErrorMessage());
            $result = false;
        }

        return $result;
    }
}