<?php
/**
 * Class checkEmail
 * @package ut
 * @subpackage validation\utUser
 */
class checkEmail extends xPDOValidationRule
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
            if(!preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/ui',$obj->{$this->field}))
            {
                $obj->xpdo->lexicon->load("usertools:validation");
                $this->validator->addMessage($this->field,'invalid',$obj->xpdo->lexicon->process('ut_err_'.$this->field));
                $result = false;
                return $result;
            }


            if($obj->scenario != 'change')
            {
                //А потом проверка на существование
                $count = $xpdo->getCount('modUserProfile', array(
                    'email' => $obj->email
                ));

                if ($count > 0)
                {
                    $obj->xpdo->lexicon->load("usertools:validation");
                    $this->validator->addMessage($this->field,'invalid',$obj->xpdo->lexicon->process('ut_err_'.$this->field.'_2'));
                    $result = false;
                }
            }
        }

        return $result;
    }
}