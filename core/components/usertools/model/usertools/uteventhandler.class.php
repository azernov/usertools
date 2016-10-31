<?

/**
 * class utEventHandler
 */
abstract class utEventHandler {
    /* @var modX $modx */
    protected $modx;

    /* @var utApplication $ut */
    protected $ut;

    public function __construct(&$modx,&$utApplication)
    {
        $this->modx = &$modx;
        $this->ut = &$utApplication;
    }

    /**
     * Функция запускает обработчик события
     * Может принимать любое количество параметров. Все они передаются в функцию process,
     * которая меняется в зависимости от потомка
     */
    public function run()
    {
        return true;
    }
}