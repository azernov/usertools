<?php
/**
 * Gets a list of users
 *
 * @param string $username (optional) Will filter the grid by searching for this
 * username
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 10.
 * @param string $sort (optional) The column to sort by. Defaults to name.
 * @param string $dir (optional) The direction of the sort. Defaults to ASC.
 *
 * @package modx
 * @subpackage processors.security.user
 */
class utUserGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'utUser';
    public $languageTopics = array('user');
    //public $permission = 'view_user';
    //public $checkListPermission = false;
    public $defaultSortField = 'username';

    public function initialize() {
        $initialized = parent::initialize();
        $this->setDefaultProperties(array(
            'usergroup' => false,
            'query' => '',
        ));
        if ($this->getProperty('sort') == 'username_link') $this->setProperty('sort','username');
        if ($this->getProperty('sort') == 'id') $this->setProperty('sort','utUser.id');
        return $initialized;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->leftJoin('modUserProfile','Profile');
        $c->leftJoin('utUserData','Data');

        $query = $this->getProperty('query','');
        if (!empty($query)) {
            $c->where(array('utUser.username:LIKE' => '%'.$query.'%'));
            $c->orCondition(array('Profile.fullname:LIKE' => '%'.$query.'%'));
            $c->orCondition(array('Data.firstname:LIKE' => '%'.$query.'%'));
            $c->orCondition(array('Data.lastname:LIKE' => '%'.$query.'%'));
            $c->orCondition(array('Profile.email:LIKE' => '%'.$query.'%'));
        }

        $c->where(array('utUser.class_key' => 'utUser'));

        $userGroup = $this->getProperty('usergroup',0);
        if (!empty($userGroup)) {
            $c->innerJoin('modUserGroupMember','UserGroupMembers');
            $c->where(array(
                'UserGroupMembers.user_group' => $userGroup,
            ));
        }
        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c) {
        $c->select($this->modx->getSelectColumns('utUser','utUser'));
        $c->select($this->modx->getSelectColumns('modUserProfile','Profile','',array('fullname','email','blocked','lastlogin','thislogin')));
        $c->select($this->modx->getSelectColumns('utUserData','Data'));
        return $c;
    }

    /**
     * Prepare the row for iteration
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $objectArray = $object->toArray();
        $objectArray['blocked'] = $object->get('blocked') ? true : false;
        $objectArray['cls'] = 'pupdate premove pcopy';
        $objectArray['lastlogin'] = date('Y-m-d H:i:s',$objectArray['lastlogin']);
        $objectArray['thislogin'] = date('Y-m-d H:i:s',$objectArray['thislogin']);
        unset($objectArray['password'],$objectArray['cachepwd'],$objectArray['salt']);
        return $objectArray;
    }
}
return 'utUserGetListProcessor';