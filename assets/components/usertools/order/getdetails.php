<?
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$miniShop2 = $modx->getService('minishop2','miniShop2',MODX_CORE_PATH.'components/minishop2/model/minishop2/');
$miniShop2->initialize('web');

$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;

if(!$id)
{
    echo json_encode(array(
        'status' => false,
        'html' => 'Не указан ID заказа'
    ));
    exit;
}

$utUser = $modx->user;

if(!$utUser instanceof utUser)
{
    echo json_encode(array(
        'status' => false,
        'html' => 'Вы не авторизованы'
    ));
    exit;
}

/**
 * @var msOrder $order
 */
if($order = $modx->getObject('msOrder',$id))
{
    if($order->get('user_id') != $utUser->id)
    {
        echo json_encode(array(
            'status' => false,
            'html' => 'Вы не можете просматривать заказ другого пользователя'
        ));
        exit;
    }

    //Все проверки пройдены, нужно показать историю заказа
    /**
     * @var msOrderProduct[] $products
     */
    $products = $order->getMany('Products');

    if(!$products)
    {
        echo json_encode(array(
            'status' => false,
            'html' => 'Невозможно получить состав заказа'
        ));
        exit;
    }

    $sql = <<<QUERY
SELECT op.*, (SELECT url FROM modx_ms2_product_files pf WHERE pf.product_id = op.product_id AND pf.path LIKE '%120x90%') as 120x90
FROM modx_ms2_order_products op
WHERE op.order_id = :order_id
QUERY;

    $query = $modx->prepare($sql);
    $query->bindValue('order_id',$id);
    $output = '';
    $chunk = $modx->getOption('ut_order_history_product_tpl');
    $outerChunk = $modx->getOption('ut_order_history_products_outer_tpl');
    if($query->execute() && $query->rowCount()>0)
    {
        while($row = $query->fetch(PDO::FETCH_ASSOC))
        {
            $row['options'] = $modx->fromJSON($row['options']);
            $row['cost'] = $miniShop2->formatPrice($row['cost']);
            $row['price'] = $miniShop2->formatPrice($row['price']);
            $output .= $modx->getChunk($chunk,$row);
        }
        $modx->getParser()->processElementTags('',$output,true,true,'[[', ']]', array(), 5);

        $orderRow = $order->toArray();



        echo json_encode(array(
            'status' => true,
            'html' => $modx->getChunk($outerChunk,array_merge(array('rows'=>$output),$orderRow)),
        ));
        exit;
    }
}

echo json_encode(array(
    'status' => false,
    'html' => 'Невозможно получить заказ'
));