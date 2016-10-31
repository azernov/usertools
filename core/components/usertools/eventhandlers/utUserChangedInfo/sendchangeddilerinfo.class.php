<?php

/**
 * Делает уведомление админа о том, что пользователь сделал запрос на изменение информации
 * Class sendChangedDilerInfo
 */
class sendChangedDilerInfo extends utEventHandler {
    /**
     * @param array $oldFields
     * @param array $newFields
     * @return void
     */
    public function run($oldFields, $newFields)
    {
        $wasChanged = false;
        $fieldsForEmail = $newFields;
        foreach($oldFields as $key => $value)
        {
            $fieldsForEmail['old.'.$key] = $value;
            if($newFields[$key] != $oldFields[$key])
            {
                $fieldsForEmail['changed.'.$key] = $newFields[$key];
                $wasChanged = true;
            }
        }
        if($wasChanged)
        {
            $subject = 'На сайте '.$this->modx->getOption('site_url').' сделан запрос на изменение личной информации';
            $body = $this->modx->getChunk($this->modx->getOption('ut_change_info_email_tpl'),$fieldsForEmail);
            $this->modx->getParser()->processElementTags('',$body,true,true,'[[',']]',array(),3);
            $this->ut->sendEmail($this->modx->getOption('ut_admin_email'),$subject,$body);
        }
    }
}