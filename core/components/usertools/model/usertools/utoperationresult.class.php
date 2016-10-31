<?php

/**
 * Class utOperationResult
 * Класс результата операции. Необходим для стандартизации результата выполнения какой-либо операции.
 * @package ut
 */
class utOperationResult
{
    public $result;
    public $messages;
    public $options;

    public function __construct($result,$messages = array(),$options = array())
    {
        $this->result = $result;
        $this->messages = $messages;
        $this->options = $options;
    }
}