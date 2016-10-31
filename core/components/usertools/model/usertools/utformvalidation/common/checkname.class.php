<?
require_once MODX_CORE_PATH . 'components/usertools/model/usertools/utformvalidation/utformvalidationrule.class.php';

/**
 * Проверка имени на корректность
 * Class checkName
 */
class checkName extends utFormValidationRule{
    protected $defaultErrorMessage = 'Укажите корректное имя';

    public function isValid($value)
    {
        $result = true;
        if(!preg_match('/^[a-zA-Zа-яА-ЯЁё]{1,40}$/ui',$value))
        {
            $this->validator->addMessage($this->fieldName,'invalid',$this->getErrorMessage());
            $result = false;
        }

        return $result;
    }
}