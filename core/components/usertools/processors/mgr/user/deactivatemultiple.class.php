<?php
require_once MODX_CORE_PATH.'model/modx/processors/security/user/adectivatemultiple.class.php';

/**
 * Deactivate multiple users
 *
 * @package modx
 * @subpackage processors.security.user
 */
class utUserDeactivateMultipleProcessor extends modUserDeactivateMultipleProcessor  {
    public function checkPermissions() {
        return true;
    }
    public function getLanguageTopics() {
        return array('user');
    }

    public function process() {
        $users = $this->getProperty('users');
        if (empty($users)) {
            return $this->failure($this->modx->lexicon('user_err_ns'));
        }
        $userIds = explode(',',$users);

        foreach ($userIds as $userId) {
            /** @var modUser $user */
            $user = $this->modx->getObject('utUser',$userId);
            if ($user == null) continue;

            $OnBeforeUserActivate = $this->modx->invokeEvent('OnBeforeUserDeactivate',array(
                'id' => $userId,
                'user' => &$user,
                'mode' => 'multiple',
            ));
            $canRemove = $this->processEventResponse($OnBeforeUserActivate);
            if (!empty($canRemove)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,$canRemove);
                continue;
            }

            $user->set('active',false);

            if ($user->save() === false) {
                $this->modx->log(modX::LOG_LEVEL_ERROR,$this->modx->lexicon('user_err_save'));
            } else {
                $this->modx->invokeEvent('OnUserDeactivate',array(
                    'id' => $userId,
                    'user' => &$user,
                    'mode' => 'multiple',
                ));
            }
        }

        return $this->success();
    }
}
return 'utUserDeactivateMultipleProcessor';