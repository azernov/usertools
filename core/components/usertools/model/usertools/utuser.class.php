<?php
require_once dirname(__FILE__).'/utworkinguser.class.php';

/**
 * @package usertools
 * @property string $class_key
 * @property utUserData $Data
 */
class utUser extends utWorkingUser {
    protected static $allowedFields = array(
        'login' => array(
            'email','password'
        ),
        'register' => array(
            'email','password','password2','company','inn','kpp','phone','mobilephone','firstname','lastname','middlename','position','type','director_fullname','address','website','accept_rules'
        ),
        'change' => array(
            'email','company','inn','kpp','phone','mobilephone','firstname','lastname','middlename','position','type','director_fullname','address','website'
        ),
        'changePassword' => array(
            'old_password','password','password2'
        ),
        'resetPassword' => array(
            'password','password2'
        )
    );

    /**
     * Искусственные поля (поля призраки). Их нет в базе данных, но их вводит пользователь и их нужно валидировать
     * @var array
     */
    protected static $ghostFields = array(
        'email' => array
        (
            'phptype' => 'string',
            'null' => false,
        ),
        'old_password' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'password2' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'company' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'inn' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'phone' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'mobilephone' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'firstname' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'lastname' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'middlename' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'position' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'type' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'director_fullname' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'address' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'website' => array
        (
            'phptype' => 'string',
            'null' => true,
        ),
        'accept_rules' => array
        (
            'phptype' => 'int',
            'null' => true,
        )
    );

    /**
     * Заполняет нужные поля и связанные модели
     * @param array $fields
     * @param bool $register
     */
    public function setSpecialFields(&$fields, $register = true)
    {
        if($register)
        {
            //Ставим имя пользователя = email
            $this->username = $this->email;

            //Пользователь неактивен до тех пор, пока его не активирует админ
            $this->active = 0;
        }

        /**
         * @var utUserData $utUserData
         */
        $utUserData = $this->xpdo->newObject('utUserData');
        $utUserData->fromArray($fields);
        $this->addOne($utUserData,'Data');

        /**
         * @var modUserProfile $profile
         */
        $profile = $this->xpdo->newObject('modUserProfile');
        $profile->fromArray($fields);
        //Запоминаем fullname
        $profile->fullname = trim($this->firstname.' '.$this->lastname);
        //$profile->city = ...; @todo сделать заполнение города
        $this->addOne($profile,'Profile');
    }

    protected function prepareFields()
    {
        parent::prepareFields();
        if($this->isFieldInScenario('password'))
        {
            //Установка пароля через set позволит сразу все сделать правильно (хеш, и т.п.)
            $this->set('password',$this->password);
        }
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public static function getControllerPath(xPDO &$modx) {
        return MODX_CORE_PATH.'components/usertools/controllers/user/';
    }
}