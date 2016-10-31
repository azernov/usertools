<?php
/**
 * Class utWorkingUser
 * Класс для пользователя, который редактируются через формы на сайте (регистрация и т.п.)
 * @package ut
 */
abstract class utWorkingUser extends modUser
{
    /*
     * Текущий сценарий работы с объектом
     * Доступные варианты определены в массиве $allowedFields
     * @var string $scenario
     */
    public $scenario;

    /**
     * Искусственные поля. Их нет в базе данных, но их вводит пользователь и их нужно валидировать
     * @var array
     */
    protected static $ghostFields = array();

    /**
     * Перечисляет разрешенные поля для разных сценариев
     * @var array
     */
    protected static $allowedFields = array();

    /**
     * Перечисляет поля, в которых может быть html код
     * @var array
     */
    protected $htmlFields = array();

    /**
     * Перечисляет разрашенные html теги, которые можно оставить в html полях
     * @var array
     */
    protected $allowedHtmlTags = array('<li>','<p>','<b>','<ul>','<ol>','<i>','<br>');

    /**
     * Возвращает разрешенные поля для сценария
     * @param $scenario
     * @return array
     */
    public static function getAllowedFields($scenario)
    {
        return isset(static::$allowedFields[$scenario]) ? static::$allowedFields[$scenario] : array();
    }

    /**
     * Удаляет из массива запрещенные для данного сценария поля
     * @param $fields
     */
    public function removeUnallowedFields(&$fields)
    {
        if(!empty($this->scenario))
        {
            foreach($fields as $key => $field)
            {
                if(!in_array($key,$this->getAllowedFields($this->scenario)))
                {
                    unset($fields[$key]);
                }
            }
        }
    }

    /**
     * Делает проверку на наличие поля $fieldName в текущем сценарии
     * @param $fieldName
     * @return bool
     */
    public function isFieldInScenario($fieldName)
    {
        return in_array($fieldName,$this->getAllowedFields($this->scenario));
    }

    /**
     * Безопасное добавление полей к объекту
     */
    public function safeFromArray(&$fldarray)
    {
        //Удаляем все запрещенные поля
        $this->removeUnallowedFields($fldarray);

        //Подмешиваем искусственные поля к метаданным, чтобы корректно заполнился массив _dirty
        $this->mixGhostFieldsToMeta();

        $this->fromArray($fldarray,'',false,true,true);
    }

    /**
     * Подмешивает к массиву _fieldMeta данные об искусственный полях
     * Это нужно для того, чтобы при валидации, массив _dirty заполнялся не только реальными полями, но и искусственными
     */
    protected function mixGhostFieldsToMeta()
    {
        foreach(static::$ghostFields as $field => $data)
        {
            $this->_fieldMeta[$field] = $data;
        }
    }

    /**
     * Удаляет из массива _fieldMeta данные об искуственных полях
     * Это нужно выполнять перед сохранением объекта, чтобы в базу данных не полетели искусственные поля и,
     * как следствие, не возникла ошибка
     */
    protected function unmixGhostFieldsToMeta()
    {
        foreach(static::$ghostFields as $field => $data)
        {
            unset($this->_fieldMeta[$field]);
        }
    }

    /**
     * Подготавливает поля после валидации
     */
    protected function prepareFields()
    {
        $this->filterFieldHtmlContent();
        $this->filterFieldModxBrackets();
    }

    /**
     * Удаляет запрещенный html код из полей, которые записаны в $htmlFields
     */
    public function filterFieldHtmlContent()
    {
        foreach($this->htmlFields as $fieldName)
        {
            //Фильтруем содержимое только тогда, когда поле содержится в сценарии
            if($this->isFieldInScenario($fieldName))
            {
                //Удаляем атрибуты у тегов
                $this->{$fieldName} = preg_replace("#(</?\w+)(?:\s(?:[^<>/]|/[^<>])*)?(/?>)#ui", '$1$2', $this->{$fieldName});
                $this->{$fieldName} = strip_tags($this->{$fieldName},implode('',$this->allowedHtmlTags));
            }
        }
    }

    public function filterFieldModxBrackets()
    {
        foreach($this->_fieldMeta as $fieldName => $fieldData)
        {
            //Находим поля, которые в сценарии и у которых строковый тип
            if($this->isFieldInScenario($fieldName) && $fieldData['phptype'] == 'string')
            {
                //Заменяем квадратные скобки и кавычки на мнемоники для любых полей (не даем вводить modx теги и не портим вывод в атрибуты типа value)
                $this->{$fieldName} = str_replace(array('[',']','"',"'"),array('&#91;','&#93;','&#34;','&#39;'),$this->{$fieldName});

                if(!in_array($fieldName,$this->htmlFields))
                {
                    //А если это поле не должно содержать html, то заменяем < > на мнемоники
                    $this->{$fieldName} = str_replace(array('<','>'),array('&lt;','&gt;'),$this->{$fieldName});
                }
            }
        }
    }

    /**
     * Проводит валидацию
     * @param array $options
     * @return bool
     */
    public function validate(array $options = array())
    {
        $result = parent::validate($options);

        //Убираем искуственные поля из метаданных, чтобы не было ошибок при сохранении
        $this->unmixGhostFieldsToMeta();

        //Вся валидация происходит внутри правил валидации (каталог validation/)

        if($result)
        {
            //Если валидация прошла успешно, то подготавливаем поля
            $this->prepareFields();
        }

        return $result;
    }

    /**
     * Метод для отладки. Выводит var_dump переменной в лог MODX
     * @param $var
     * @param bool $toModxLog
     */
    protected function _debug($var,$toModxLog = true)
    {
        ob_start();
        var_dump($var);
        $out = ob_get_clean();
        $this->xpdo->log(MODX_LOG_LEVEL_ERROR,$out);
    }
}