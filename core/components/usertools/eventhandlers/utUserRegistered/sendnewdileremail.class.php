<?php

/**
 * Уведомляет пользователя и админа о регистрации
 * Class sendNewDilerEmail
 */
class sendNewDilerEmail extends utEventHandler {
    /**
     * @param utUser $utUser
     * @return string
     */
    public function run(&$utUser)
    {
        $userArray = $utUser->toArray();
        $userArray = array_merge($userArray,$utUser->Profile->toArray());
        $userArray = array_merge($userArray,$utUser->Data->toArray());

        //Письмо админу
        $subject = 'На сайте '.$this->modx->getOption('site_url').' зарегистрировался новый пользователь';
        $body = $this->modx->getChunk($this->modx->getOption('ut_new_registration_email_tpl'),$userArray);
        $this->modx->getParser()->processElementTags('',$body,true,true,'[[',']]',array(),3);
        $this->ut->sendEmail($this->modx->getOption('ut_admin_email'),$subject,$body);

        //Письмо клиенту
        $subject = 'Вы зарегистрировались на сайте '.$this->modx->getOption('site_url').'';
        $body = $this->modx->getChunk($this->modx->getOption('ut_new_registration_email_user_tpl'),$userArray);
        $this->modx->getParser()->processElementTags('',$body,true,true,'[[',']]',array(),3);
        $this->ut->sendEmail($userArray['email'],$subject,$body);
    }
}