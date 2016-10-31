<?

/**
 * Класс события
 * Class utEvent
 */
class utEvent {
    /* @var modX $modx */
    protected $modx;

    /* @var utApplication $ut */
    protected $ut;

    public $config;

    protected $handlers = array();

    /**
     * @param $modx
     * @param $ut
     * @param array $config
     */
    public function __construct(&$modx, &$ut, $config = array())
    {
        $this->modx = &$modx;
        $this->ut = &$ut;
        $this->config = $config;
        //Подключаем абстрактный класс
        require_once dirname(__FILE__).'/uteventhandler.class.php';
    }

    /**
     * Выполняет поиск обработчиков события в каталоге с обработчиками
     * Для добавления обработчика необходимо создать в папке eventhandlers подпапку с названием события (регистр важен)
     * В ней нужно разместить файл НАЗВАНИЕ_КЛАССА.class.php и внутри описать класс-наследник от utEventHandler.
     * @param string $event
     * @return utEventHandler[]
     */
    private function getEventHandlers($event)
    {
        if(trim($event) == '') return array();

        //Если мы уже все подключили ранее, то вернем то, что есть
        if(isset($this->handlers[$event])) return $this->handlers[$event];

        //Иначе начинаем поиск обработчиков в папке core/components/usertools/eventhandlers/
        $path = dirname(dirname(dirname(__FILE__))).'/eventhandlers/';

        $handlers = array();
        //not in main chunks folder, so search in category directories
        $handlerFiles = glob($path.$event.'/*.class.php');
        foreach ($handlerFiles as $handlerFile) {
            if(file_exists($handlerFile) && !is_dir($handlerFile)) {
                require_once($handlerFile);
                $content = file_get_contents($handlerFile);
                //Имя класса маленькими буквами
                $classNameSmall = str_replace('.class.php','',end(explode('/',$handlerFile)));

                $matches = array();
                preg_match("/class +({$classNameSmall})/i",$content,$matches);
                $className = $matches[1];

                $handler = new $className($this->modx,$this->ut);
                if($handler instanceof utEventHandler)
                {
                    $handlers[] = $handler;
                }
            }
        }

        //Запоминаем всех обработчиков в массив
        $this->handlers[$event] = $handlers;

        return $this->handlers[$event];
    }

    /**
     * Вызывает событие и запускает все обработчики
     * @param $event
     * @param array $args
     */
    public function fire($event, $args = array())
    {
        $handlers = $this->getEventHandlers($event);
        if(count($handlers) > 0)
        {
            foreach($handlers as $handler)
            {
                call_user_func_array(array($handler,"run"), $args);
            }
        }
    }
}