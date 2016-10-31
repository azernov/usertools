<?php
require_once MODX_CORE_PATH.'model/modx/processors/security/user/update.class.php';

class utUserUpdateProcessor extends modUserUpdateProcessor {
    public $classKey = 'utUser';

    /**
     * @var utUserData $utData
     */
    public $utData;

    public function checkPermissions()
    {
        return true;
    }

    public function beforeSave()
    {
        $this->setElvData();
        return parent::beforeSave();
    }

    public function setElvData()
    {
        $this->utData = $this->object->getOne('Data');
        if (empty($this->utData)) {
            $this->utData = $this->modx->newObject('utUserData');
            $this->utData->set('internalKey',$this->object->get('id'));
            $this->utData->save();
            $this->object->addOne($this->utData,'Data');
        }
        $this->utData->fromArray($this->getProperties());
        return $this->utData;
    }

    public function afterSave()
    {
        parent::afterSave();
        //Проверяем была ли активация пользователя.
        if($this->activeStatusChanged && $this->newActiveStatus)
        {
            if(!isset($this->modx->ut) || !$this->modx->ut instanceof utApplication)
            {
                $this->modx->getService('ut','utApplication',MODX_CORE_PATH.'components/usertools/model/usertools/');
            }

            /**
             * @var utApplication $ut
             */
            $ut = &$this->modx->ut;

            //Генерируем событие об активации пользователя
            $ut->utEvent->fire('utUserActivated',array(
                &$this->object
            ));
        }
    }
}
return 'utUserUpdateProcessor';
