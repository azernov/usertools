<?php
require_once MODX_CORE_PATH.'model/modx/processors/security/user/activatemultiple.class.php';

/**
 * Activate multiple users
 *
 * @package modx
 * @subpackage processors.security.user
 */
class utUserActivateMultipleProcessor extends modUserActivateMultipleProcessor
{
    public function checkPermissions()
    {
        return true;
    }

    public function getLanguageTopics()
    {
        return array('user');
    }

    public function process()
    {
        $users = $this->getProperty('users');
        if (empty($users))
        {
            return $this->failure($this->modx->lexicon('user_err_ns'));
        }
        $userIds = explode(',', $users);

        if (!isset($this->modx->ut) || !$this->modx->ut instanceof utApplication)
        {
            $this->modx->getService('ut', 'utApplication', MODX_CORE_PATH . 'components/usertools/model/usertools/');
        }

        /**
         * @var utApplication $ut
         */
        $ut = &$this->modx->ut;

        foreach ($userIds as $userId)
        {
            /** @var modUser $user */
            $user = $this->modx->getObject('utUser', $userId);

            if ($user == null) continue;

            $OnBeforeUserActivate = $this->modx->invokeEvent('OnBeforeUserActivate', array(
                'id' => $userId,
                'user' => &$user,
                'mode' => 'multiple',
            ));
            $canRemove = $this->processEventResponse($OnBeforeUserActivate);
            if (!empty($canRemove))
            {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $canRemove);
                continue;
            }

            $user->set('active', true);

            if ($user->save() === false)
            {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('user_err_save'));
            }
            else
            {
                //Генерируем событие об активации пользователя
                $ut->utEvent->fire('utUserActivated', array(
                    &$user
                ));

                $this->modx->invokeEvent('OnUserActivate', array(
                    'id' => $userId,
                    'user' => &$user,
                    'mode' => 'multiple',
                ));
            }
        }

        return $this->success();
    }
}

return 'utUserActivateMultipleProcessor';