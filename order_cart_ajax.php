<?php
require_once "common.php";

$mem_id = $_POST['mem_id'];
$state = $_POST['state'];

try{

  if($state == "order_cart"){
    $sql = "SELECT * FROM cart WHERE mem_id = '{$mem_id}' AND state = 'Y'";
    $result = sql_query($sql);
  } else {
    $cart_idx = $_POST['cart_idx'];
    $sql = "SELECT * FROM cart WHERE mem_id = '{$mem_id}' AND idx IN (".$cart_idx.") AND state = 'Y'";
    $result = sql_query($sql);
  }

  $data = array();

  while ($row = sql_fetch_array($result)) {
    $sql = "SELECT * FROM product WHERE idx = '{$row['product_idx']}'";
    $data[$row['idx']]['cart'] = $row;
    $data[$row['idx']]['product'] = sql_fetch($sql);

    // product 의 method 가 공백일 경우
    if($data[$row['idx']]['product']['method'] == '') {
      $data[$row['idx']]['product']['method'] = '-';
    }

    if($data[$row['idx']]['product']['unit_price'] == '') {
      $data[$row['idx']]['product']['unit_price'] = '-';
    }
  }
  echo json_encode($data);

} catch (Exception $e) {
  echo json_encode($e);
}

?>