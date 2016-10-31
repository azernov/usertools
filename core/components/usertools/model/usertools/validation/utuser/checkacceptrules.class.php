<?php
/**
 * Class checkAcceptRules
 * @package ut
 * @subpackage validation\utUser
 */
class checkAcceptRules extends xPDOValidationRule
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
            if($obj->{$this->field} != 1)
            {
                $obj->xpdo->lexicon->load("usertools:validation");
                $this->validator->addMessage($this->field,'invalid',$obj->xpdo->lexicon->process('ut_err_accept_rules'));
                $result = false;
            }
        }

        return $result;
    }
}