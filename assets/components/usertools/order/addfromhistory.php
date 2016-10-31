<?
define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;

if(!$id)
{
    echo json_encode(array(
        'status' => false,
        'message' => 'Не указан ID заказа'
    ));
    exit;
}

$utUser = $modx->user;

if(!$utUser instanceof utUser)
{
    echo json_encode(array(
        'status' => false,
        'message' => 'Вы не авторизованы'
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
            'message' => 'Вы не можете просматривать заказ другого пользователя'
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
            'message' => 'Невозможно получить состав заказа'
        ));
        exit;
    }

    /**
     * @var miniShop2 $miniShop2
     */
    $miniShop2 = $modx->getService('minishop2','miniShop2',MODX_CORE_PATH.'components/minishop2/model/minishop2/');
    $miniShop2->initialize('web');

    $missingProducts = array();

    //Собираем id продуктов в заказе для будущего запроса
    foreach($products as $product)
    {
        /* @var msProduct $msProduct */
        $msProduct = $product->getOne('Product');
        /* @var msProductData $data */
        $Data = $msProduct->getOne('Data');
        if($msProduct && $msProduct->published == 1 && $msProduct->deleted != 1)
        {
            //Добавляем товар в корзину только если он существует, опубликован и не удален
            $miniShop2->cart->add($product->get('product_id'),$product->get('count'),$product->get('options'));
        }
        else
        {
            $missingProducts[] = $msProduct->get('pagetitle');
            //Сразу же удаляем из корзины все эти товары, если вдруг они там были из предыдущих разов
            $cart = $miniShop2->cart->get();
            foreach($cart as $key => $item)
            {
                if($item['id'] == $msProduct->id)
                {
                    $miniShop2->cart->remove($key);
                }
            }
        }
    }

    $status = $miniShop2->cart->status();

    echo json_encode(array_merge($status,array(
        'status' => true,
        'message' => empty($missingProducts) ? '' : 'Извините, в данный момент у нас отсутствуют следующие позиции:',
        'missing_products' => $missingProducts
    )));
    exit;
}

echo json_encode(array(
    'status' => false,
    'message' => 'Невозможно получить заказ'
));