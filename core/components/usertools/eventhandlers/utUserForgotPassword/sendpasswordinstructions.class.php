<?php

/**
 * Высылает письмо с инструкцией по восстановлению пароля
 * Class sendPasswordInstructions
 */
class sendPasswordInstructions extends utEventHandler {
    /**
     * @param utUser $utUser
     * @return void
     */
    public function run(&$utUser)
    {
        $code = $utUser->Data->activation_code;

        $subject = 'На сайте '.$this->modx->getOption('site_url').' сделан запрос на восстановление пароля';
        $body = $this->modx->getChunk($this->modx->getOption('ut_forgot_password_email_tpl'),array('code' => $code));
        $this->modx->getParser()->processElementTags('',$body,true,true,'[[',']]',array(),3);
        $this->ut->sendEmail($utUser->Profile->email,$subject,$body);
    }
}