<?
/**
 * Снипет выводит историю заказов для текущего пользователя
 * $tpl
 * $tplEmpty
 */

if(!$modx->user instanceof utUser) return '';

$miniShop2 = $modx->getService('minishop2','miniShop2',MODX_CORE_PATH.'components/minishop2/model/minishop2/');



$tpl = $modx->getOption('tpl',$scriptProperties,false);
$tplEmpty = $modx->getOption('tplEmpty',$scriptProperties,false);

if(!$tpl) return '';


$sql = <<<QUERY
SELECT
    o.*,
    s.`name` as `status_name`,
    s.`color` as `status_color`,
    (SELECT SUM(`count`) FROM modx_ms2_order_products op WHERE op.order_id = o.id) `count`
FROM modx_ms2_orders o
JOIN modx_ms2_order_statuses s ON s.id = o.status
WHERE o.user_id = :user_id
ORDER BY o.id DESC
QUERY;

$query = $modx->prepare($sql);

$query->bindValue('user_id',$modx->user->id);

$output = '';
if($query->execute())
{
    while($row = $query->fetch(PDO::FETCH_ASSOC))
    {
        $row['cost'] = $miniShop2->formatPrice($row['cost']);
        $row['cart_cost'] = $miniShop2->formatPrice($row['cart_cost']);
        $row['delivery_cost'] = $miniShop2->formatPrice($row['delivery_cost']);
        $output .= $modx->getChunk($tpl,$row);
    }
}

if($output == '' && $tplEmpty)
{
    $output = $modx->getChunk($tplEmpty);
}

return $output;