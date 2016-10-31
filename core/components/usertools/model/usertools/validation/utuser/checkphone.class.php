<?php
/**
 * Class checkPhone
 * @package usertools
 * @subpackage validation\utUser
 */
class checkPhone extends xPDOValidationRule
{
    public function isValid($value, array $options = array()) {
        parent::isValid($value, $options);
        $result = true;

        /* @var utUser $obj */
        $obj = &$this->validator->object;


        if($obj->isFieldInScenario($this->field) && !empty($obj->{$this->field}))
        {
            //Удаляем всякие скобочки и тире из номера
            if(!preg_match('/^\+?[0-9]\([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}$/ui',$obj->{$this->field}))
            {
                $obj->xpdo->lexicon->load("usertools:validation");
                $this->validator->addMessage($this->field,'invalid',$obj->xpdo->lexicon->process('ut_err_'.$this->field));
                $result = false;
            }
        }

        return $result;
    }
}