<?php
require_once MODX_CORE_PATH.'model/modx/processors/security/user/get.class.php';

/**
 * @package modx
 * @subpackage processors.security.user
 */
/**
 * Get a user
 */
class utUserGetProcessor extends modUserGetProcessor {
    public $classKey = 'utUser';

    public function checkPermissions()
    {
        return true;
    }

    public function cleanup() {
        $userArray = $this->object->toArray();

        $profile = $this->object->getOne('Profile');
        if ($profile) {
            $userArray = array_merge($profile->toArray(),$userArray);
        }

        $utUserData = $this->object->getOne('Data');
        if ($utUserData)
        {
            $userArray = array_merge($utUserData->toArray(),$userArray);
        }

        $userArray['dob'] = !empty($userArray['dob']) ? strftime('%m/%d/%Y',$userArray['dob']) : '';
        $userArray['blockeduntil'] = !empty($userArray['blockeduntil']) ? strftime('%Y-%m-%d %H:%M:%S',$userArray['blockeduntil']) : '';
        $userArray['blockedafter'] = !empty($userArray['blockedafter']) ? strftime('%Y-%m-%d %H:%M:%S',$userArray['blockedafter']) : '';
        $userArray['lastlogin'] = !empty($userArray['lastlogin'])
            ? date(
                $this->modx->getOption('manager_date_format') .', '. $this->modx->getOption('manager_time_format'),
                $userArray['lastlogin']
            )
            : '';
        $userArray['thislogin'] = !empty($userArray['thislogin'])
            ? date(
                $this->modx->getOption('manager_date_format') .', '. $this->modx->getOption('manager_time_format'),
                $userArray['thislogin']
            )
            : '';

        unset($userArray['password'],$userArray['cachepwd']);
        return $this->success('',$userArray);
    }

}
return 'utUserGetProcessor';
