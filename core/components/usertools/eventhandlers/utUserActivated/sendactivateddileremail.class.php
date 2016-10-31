<?php

/**
 * Уведомляет пользователя об активации
 * Class sendNewDilerEmail
 */
class sendActivatedDilerEmail extends utEventHandler {
    /**
     * @param utUser $utUser
     * @return bool
     */
    public function run(&$utUser)
    {
        //отправляем письмо пользователю
        $subject = 'Ваш аккаунт активирован';
        $body = $this->modx->getChunk($this->modx->getOption('ut_activated_email_tpl'),$utUser->toArray());


        /**
         * @var utApplication $ut
         */
        $ut = &$this->ut;
        $ut->sendEmail($utUser->Profile->email,$subject,$body);

        return true;
    }
}