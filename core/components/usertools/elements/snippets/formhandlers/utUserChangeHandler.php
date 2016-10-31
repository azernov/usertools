<?
/**
 * Снипет обработчик данных от формы изменения личной информации
 */

//Если не прилетела переменная, которая должна быть отправлена, то ничего не делаем - выдаем ошибку!
if (!isset($_POST[$scriptProperties['submitVar']])) {
    $response = array(
        'success' => false,
        'message' => 'Ошибка отправки',
        'data' => array()
    );

    return $modx->toJSON($response);
}


$userFields = $_POST['User'];

/**
 * @var utApplication $usertools
 */
$usertools = $modx->getService('usertools', 'utApplication', MODX_CORE_PATH . 'components/usertools/model/usertools/');

$userHandler = $usertools->getUserHandler();

$result = $userHandler->change($userFields);

$status = $result->result;


$errors = array();
$resultMessage = $scriptProperties['successMessage'];

if (!$result->result) {
    $resultMessage = '';
    foreach ($result->messages as $message) {
        $field = 'User[' . $message['field'] . ']';
        $errors[$field] = $message['message'];
        $resultMessage .= $message['message'].'; ';
    }
    if (trim($resultMessage) == '') {
        $resultMessage = $scriptProperties['validationErrorMessage'];
    }
}

$response = array(
    'success' => $status,
    'message' => $resultMessage,
    'data' => $errors,
    'options' => $result->options
);

return $modx->toJSON($response);