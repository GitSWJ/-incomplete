<?php
require_once "common.php";

$mem_id = $_POST['mem_id'];
$category = $_POST['category'];
$company = $_POST['company'];
$name = $_POST['name'];
$tel = $_POST['tel'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$memo = $_POST['memo'];
$cart_idx = $_POST['cart_idx'];

try{
  // $cart_idx에서 , 를 기준으로 배열로 만든다.
  $cart_idx = explode(',', $cart_idx);

  sql_query("begin");

  // order_num 생성(YYYYMMDD-0000)
  $order_num = date('Ymd').'-';
  $order_num_sql = "SELECT MAX(order_num) as order_num FROM `order` WHERE order_num LIKE '".date('Ymd')."%'";
  $order_num_result = sql_query($order_num_sql);
  $row = sql_fetch_array($order_num_result);
  if($row['order_num'] == '') {
    $order_num .= '0001';
  } else {
    $order_num .= sprintf('%04d', substr($row['order_num'], 9) + 1);
  }

  // cart_idx가 배열로 넘어왔을 경우
  if(is_array($cart_idx)) {

    // cart_idx 로 product 테이블에서 상품 정보를 가져온다.
    $product_sql = "SELECT product_idx,qty,unit_length FROM cart WHERE idx IN (".implode(',', $cart_idx).")";
    $product_result = sql_query($product_sql);

    // product 정보로 상품의 재고를 수정한다.
    while($row = sql_fetch_array($product_result)) {
      $stock = $row['qty'] * $row['unit_length'];
      $stock_sql = "UPDATE product SET stock = stock - $stock WHERE idx = '{$row['product_idx']}'";
      $stock_result = sql_query($stock_sql);
      if(!$stock_result) {
        throw new Exception('상품의 재고를 수정하는데 실패했습니다.');
      }
    }

    $sql = "UPDATE cart SET state = 'D' WHERE idx IN (".implode(',', $cart_idx).") AND mem_id = '$mem_id'";
    $result = sql_query($sql);

    foreach($cart_idx as $val) {
      $sql = "INSERT INTO `order`
       (mem_id, category, company, name, tel, phone, email, memo, cart_idx, order_num) VALUES
       ('$mem_id', '$category', '$company', '$name', '$tel', '$phone', '$email', '$memo', '$val', '$order_num')";
      $result = sql_query($sql);

      if($result) {
        sql_query("commit");
        $data = array('result' => 'success');
      }else{
        sql_query("rollback");
        $data = array('result' => 'fail');
      }
    }
  } else {
    $sql = "INSERT INTO `order`
       (mem_id, category, company, name, tel, phone, email, memo, cart_idx, order_num) VALUES
       ('$mem_id', '$category', '$company', '$name', '$tel', '$phone', '$email', '$memo', '$cart_idx', '$order_num')";
    $result = sql_query($sql);

    if($result) {
      sql_query("commit");
      $data = array('result' => 'success');
    }else{
      sql_query("rollback");
      $data = array('result' => 'fail');
    }
  }
  echo json_encode($data);
  
} catch (Exception $e) {
  $data = array('result' => 'fail');
  $data['msg'] = $e;
  echo json_encode($data);
}