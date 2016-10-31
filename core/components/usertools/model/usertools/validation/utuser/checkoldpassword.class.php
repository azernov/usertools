<?php
/**
 * Class checkOldPassword
 * @package ut
 * @subpackage validation\utUser
 */
class checkOldPassword extends xPDOValidationRule
{
    public function isValid($value, array $options = array())
    {
        parent::isValid($value, $options);
        $result = true;

        /* @var utUser $obj */
        $obj = &$this->validator->object;
        $xpdo = &$obj->xpdo;

        if($obj->isFieldInScenario($this->field))
        {
            //Проверяем совпадает ли пароль со старым (в $value уже зашифрованный пароль)
            if(strcmp($obj->password,$value) === 0)
            {
                $obj->xpdo->lexicon->load("usertools:validation");
                $this->validator->addMessage($this->field,'invalid',$obj->xpdo->lexicon->process('ut_err_'.$this->field));
                $result = false;
            }
        }

        return $result;
    }
}