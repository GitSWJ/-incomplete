<?php
require_once "common.php";

$product_idx = $_POST['product_idx'];
$mem_id = $_POST['mem_id'];
$length = $_POST['length'];
$order_qty = $_POST['order_qty'];
$option = $_POST['option'];

try{

  $price_sql = "SELECT unit_price FROM product WHERE idx = '{$product_idx}'";
  $price_result = sql_query($price_sql);
  $price_row = sql_fetch_array($price_result);
  $unit_price = $price_row['unit_price']*$length;
  
  // 데이터베이스 연결
  $sql = "INSERT INTO `cart` (`mem_id`, `product_idx`, `unit_length`, `unit_price` , `qty`, `option`, `state`) VALUES ('$mem_id', '$product_idx', '$length', '$unit_price', '$order_qty', '$option', 'Y')";

  // INSERT 쿼리 실행
  $result = sql_query($sql);

  // 등록한 cart정보의 idx를 가져옴
  $last_idx = sql_insert_id();

  // $_POST['pass'] 의 값이 있고 그 값이 Y 인 경우
  if($_POST['pass'] == 'Y') {
    // 가져온 idx를 배열에 담아서 반환
    $data = array('idx' => $last_idx);
  }

  if($result == false) {
    throw new Exception('장바구니에 상품을 담지 못했습니다.');
  }else{
    $data['state'] = 'success';
  }

  echo json_encode($data);
} catch (Exception $e) {
  echo json_encode($e);
}
?>