<?

/**
 * Класс-обработчик действий над пользователем
 * Class utUserHandler
 */
class utUserHandler{
    /* @var Modx $modx */
    private $modx;

    /* @var utApplication $usertools */
    private $usertools;

    private $config = array();

    /**
     * @param Modx $modx
     * @param utApplication $utApplication
     */
    public function __construct(&$modx, &$utApplication, $config = array())
    {
        $this->modx = &$modx;
        $this->usertools = &$utApplication;
        $this->config = array_merge($this->config,$config);
    }

    /**
     * @param $email
     * @param $password
     * @param array $loginContexts
     * @param $rememberme
     * @return bool|utOperationResult
     */
    public function login($email = '', $password = '', $loginContexts = array(), $rememberme = true)
    {
        /**
         * @var utUser $user
         */
        $user = $this->modx->getObject('utUser',array(
            'username' => $email,
        ));
        $result = true;
        if($user && $user->passwordMatches($password) && !$user->Profile->blocked && $user->active)
        {
            $loginContexts = empty($loginContexts) ? explode(',',$this->modx->getOption('ut_auth_contexts')) : $loginContexts;
            if(empty($loginContexts)) $loginContexts = array('web');
            //Делаем обход по всем контекстам и авторизуемся в каждом!
            foreach($loginContexts as $loginContext)
            {
                //Авторизуем пользователя
                $user->addSessionContext($loginContext);
                $user->loadAttributes(array('modAccessContext'),$loginContext,true);

                //Запоминаем (если необходимо)
                if ($rememberme) {
                    $_SESSION['modx.' . $loginContext . '.session.cookie.lifetime']= $this->modx->getOption('ut_login_lifetime');
                } else {
                    $_SESSION['modx.' . $loginContext . '.session.cookie.lifetime']= 0;
                }
                $result = $result && true;
            }
        }
        else
        {
            $result = false;
        }
        if($result)
        {
            return new utOperationResult(true,array(
                array(
                    'field' => 'email',
                    'message' => 'Вы успешно авторизованы'
                )
            ),array(
                'redirect_url' => $this->modx->makeUrl($this->modx->getOption('ut_profile_page'))
            ));
        }

        if($user && !$user->active){
            return new utOperationResult(false,array(
                array(
                    'field' => 'email',
                    'message' => 'Ваша учетная еще не активирована'
                )
            ));
        }
        elseif($user && $user->active){
            return new utOperationResult(false,array(
                array(
                    'field' => 'password',
                    'message' => 'Пароль указан неверно'
                )
            ));
        }

        return new utOperationResult(false,array(
            array(
                'field' => 'email',
                'message' => 'Учетная запись не найдена'
            )
        ));
    }

    /**
     * @param array $logoutContexts
     * @return utOperationResult
     */
    public function logout($logoutContexts = array())
    {
        $logoutContexts = empty($logoutContexts) ? explode(',',$this->modx->getOption('ut_auth_contexts')) : $logoutContexts;
        if(empty($logoutContexts)) $logoutContexts = array('web');
        $result = true;
        //Делаем обход по всем контекстам и выходим из каждого!
        foreach($logoutContexts as $logoutContext)
        {
            if($this->modx->user && $this->modx->user->hasSessionContext($logoutContext) && $this->modx->user->class_key=='utUser')
            {
                $this->modx->user->removeSessionContext($logoutContext);

                /**
                 * @var miniShop2 $miniShop2
                 */
                $miniShop2 = $this->modx->getService('minishop2','miniShop2',MODX_CORE_PATH.'components/minishop2/model/minishop2/');
                $miniShop2->initialize($logoutContext);

                //Очищаем корзину
                $miniShop2->cart->clean();

                $result = $result && true;
            }
            else
            {
                $result = $result && false;
            }
        }
        if($result)
        {
            return new utOperationResult(true,array(
                array(
                    'field' => 'email',
                    'message' => 'Вы успешно деавторизованы'
                )
            ),array(
                'redirect_url' => $this->modx->makeUrl($this->modx->getOption('site_start'))
            ));
        }
        return new utOperationResult(false,array(
            array(
                'message' => 'Вы не авторизованы'
            )
        ),array(
            'redirect_url' => $this->modx->makeUrl($this->modx->getOption('site_start'))
        ));
    }

    /**
     * Регистрирует пользователя
     * @param array $fields
     * @return utOperationResult
     */
    public function register($fields)
    {
        /* @var utUser $utUser */
        $utUser = $this->modx->newObject('utUser');
        $utUser->scenario = 'register';

        //Ставим ровные значения, чтобы сделать нужные подготовки и проверки
        //Также в этом методе добавлено удаление запрещенных для сценария полей
        //После его вызова из массива $fields исчезнут все запрещенные поля
        $utUser->safeFromArray($fields);

        $fieldsForEmail = $fields;

        //Подготавливаем поля перед сохранением
        $utUser->setSpecialFields($fields);

        $saveResult = $utUser->save();

        $fieldsForEmail = array_merge($utUser->_fields,$fieldsForEmail);

        if($saveResult)
        {
            //Генерируем событие, говорящее о том, что пользователь успешно зарегистрировался
            $this->usertools->utEvent->fire('utUserRegistered',array(
                &$utUser, $fieldsForEmail
            ));
            //И добавляем пользователя в группу
            $group = $this->modx->getOption('ut_user_group');
            $role = $this->modx->getOption('ut_user_group_role', null, '');
            if($group && $role){
                $utUser->joinGroup($this->modx->getOption('ut_user_group'),$this->modx->getOption('ut_user_group_role'));
            }
        }

        $options = array();
        if($saveResult)
        {
            $options = array(
                'redirect_url' => $this->modx->getOption('site_url'),
                'redirect_timeout' => 5000
            );
        }

        return new utOperationResult($saveResult,$utUser->_validator->getMessages(),$options);
    }

    /**
     * Запрос на изменение личной информации
     * @param $fields
     * @return utOperationResult
     */
    public function change($fields)
    {
        /* @var utUser $utUser */
        $utUser = $this->modx->user;

        if(!$utUser instanceof utUser)
        {
            return new utOperationResult(false,array(
                array(
                    'field' => 'email',
                    'message' => 'Вы не авторизованы'
                )
            ));
        }

        $oldFields = $utUser->toArray();
        $oldFields = array_merge($oldFields,$utUser->Profile->toArray());
        $oldFields = array_merge($oldFields,$utUser->Data->toArray());
        $utUser->removeUnallowedFields($oldFields);

        $utUser->scenario = 'change';

        //Ставим ровные значения, чтобы сделать нужные подготовки и проверки
        //Также в этом методе добавлено удаление запрещенных для сценария полей
        //После его вызова из массива $fields исчезнут все запрещенные поля
        $utUser->safeFromArray($fields);

        //Подготавливаем специальные поля
        $utUser->setSpecialFields($fields,false);

        $validationResult = $utUser->validate();

        $newFields = $utUser->toArray();
        $newFields = array_merge($newFields,$utUser->Profile->toArray());
        $newFields = array_merge($newFields,$utUser->Data->toArray());
        $utUser->removeUnallowedFields($newFields);

        //Делаем проверку на то, были ли какие-либо поля изменены
        $wasChanged = false;
        foreach($newFields as $key => $value)
        {
            if($newFields[$key] != $oldFields[$key])
            {
                $wasChanged = true;
                //$this->modx->log(MODX_LOG_LEVEL_ERROR,$key);
                break;
            }
        }

        if(!$wasChanged)
        {
            return new utOperationResult(false,array(
                array(
                    'message' => 'Вы не изменили ни одного поля'
                )
            ));
        }

        if($validationResult)
        {
            $this->usertools->utEvent->fire('utUserChangedInfo',array(
                $oldFields, $newFields
            ));
        }

        return new utOperationResult($validationResult,$utUser->_validator->getMessages());
    }

    /**
     * Генерирует ключ активации для смены пароля
     */
    public function forgotPassword($email)
    {
        /* @var utUser $utUser */
        $utUser = $this->modx->getObject('utUser',array(
            'username' => $email
        ));

        if(!$utUser instanceof utUser)
        {
            return new utOperationResult(false,array(
                array(
                    'field' => 'email',
                    'message' => 'Пользователь с таким email не найден'
                )
            ));
        }

        //Генерируем ключ активации
        $code = $utUser->generatePassword();
        $utUser->Data->set('activation_code',$code);

        if($utUser->Data->save())
        {
            //И генерируем событие
            $this->usertools->utEvent->fire('utUserForgotPassword',array(
                &$utUser
            ));

            return new utOperationResult(true,array(
                array(
                    'message' => 'На вашу почту выслано письмо с инструкцией по изменению пароля'
                )
            ));
        }

        return new utOperationResult(false,array(
            array(
                'field' => 'email',
                'message' => 'Не удалось создать ключ активации. Повторите попытку позже.'
            )
        ));
    }

    public function changePassword($fields)
    {
        $messages = array();
        $saveResult = false;

        /* @var utUser $utUser */
        $utUser = $this->modx->user;

        if(!$utUser instanceof utUser)
        {
            return new utOperationResult(false,array(
                array(
                    'field' => 'email',
                    'message' => 'Вы не авторизованы'
                )
            ));
        }

        $utUser->scenario = 'changePassword';
        $utUser->safeFromArray($fields);
        $saveResult = $utUser->save();
        $messages = $utUser->_validator->getMessages();

        return new utOperationResult($saveResult,$messages);
    }

    /**
     * Выполняет смену пароля пользователя через функцию "Забыл пароль"
     * @param $fields
     * @return utOperationResult
     */
    public function resetPassword($fields, $activationCode)
    {
        $sql = <<<QUERY
SELECT id FROM modx_ut_users WHERE activation_code = :code AND activation_code != ''
QUERY;

        $query = $this->modx->prepare($sql);
        $query->bindValue('code',$activationCode);
        if($query->execute() && $query->rowCount()>0)
        {
            //Получили ID пользователя с таким кодом активации
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $userId = $row['id'];
            /* @var utUser $utUser */
            $utUser = $this->modx->getObject('utUser',$userId);
            $utUser->scenario = 'changePassword';

            $utUser->safeFromArray($fields);
            $saveResult = $utUser->save();

            if($saveResult)
            {
                //Нужно сбросить код активации
                /**
                 * @var utUserData $data
                 */
                $data = $this->modx->getObject('utUserData',array(
                    'id' => $userId
                ));
                $data->activation_code = '';
                $data->save();


                //Генерируем событие, говорящее о том, что пользователь успешно зарегистрировался
                $this->usertools->utEvent->fire('utUserPasswordChanged',array(
                    &$utUser
                ));
            }

            $options = array();

            return new utOperationResult($saveResult,$utUser->_validator->getMessages(),$options);
        }

        return new utOperationResult(false,array(
            array(
                'field' => 'email',
                'message' => 'Пользователь не найден'
            )
        ));

    }

    public function saveToSession($data)
    {
        if(!isset($_SESSION['utUser']))
        {
            $_SESSION['utUser'] = array();
        }

        foreach($data as $key => $value)
        {
            $_SESSION['utUser'][$key] = $value;
        }
    }

    public function getFromSession($key)
    {
        return isset($_SESSION['utUser'][$key]) ? $_SESSION['utUser'][$key] : null;
    }

    public function saveUserLocationToSession($country,$city = '')
    {
        $this->saveToSession(array(
            'location' => array(
                'country' => $country,
                'city' => $city
            )
        ));
    }

    public function getUserLocationFromSession()
    {
        return $this->getFromSession('location');
    }


    private function _debug($var,$toModxLog = true)
    {
        ob_start();
        var_dump($var);
        $out = ob_get_clean();
        $this->modx->log(MODX_LOG_LEVEL_ERROR,$out);
    }
}