<?php
/**
 * Class checkCompany
 * @package ut
 * @subpackage validation\utUser
 */
class checkCompany extends xPDOValidationRule
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
            //Сначала проверка на соответствие регулярке
            if($obj->type == 'ul' && !preg_match('/^[А-Яа-яЁёA-z0-9\- \"\'\(\)]{3,}$/ui',$obj->{$this->field}))
            {
                $obj->xpdo->lexicon->load("usertools:validation");
                $this->validator->addMessage($this->field,'invalid',$obj->xpdo->lexicon->process('ut_err_'.$this->field));
                $result = false;
            }
        }

        return $result;
    }
}