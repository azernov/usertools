<?php
/**
 * Class checkName
 * @package usertools
 * @subpackage validation\utUser
 */
class checkName extends xPDOValidationRule
{
    public function isValid($value, array $options = array()) {
        parent::isValid($value, $options);
        $result = true;

        /* @var utUser $obj */
        $obj = &$this->validator->object;


        if($obj->isFieldInScenario($this->field))
        {
            if(!($this->field == 'middlename' && empty($value)) && !preg_match('/^[a-zA-Zа-яА-ЯЁё]{1,40}$/ui',$obj->{$this->field}))
            {
                $obj->xpdo->lexicon->load("usertools:validation");
                $this->validator->addMessage($this->field,'invalid',$obj->xpdo->lexicon->process('ut_err_'.$this->field));
                $result = false;
            }
        }

        return $result;
    }
}