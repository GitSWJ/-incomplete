<?php
require_once "common.php";

$mem_id = $_POST['mem_id'];
$state = $_POST['state'];
$cart_idx = $_POST['cart_idx'];

try{
  sql_query("begin");

  // cart_idx가 배열로 넘어왔을 경우
  if(is_array($cart_idx)) {
    foreach($cart_idx as $val) {
      $sql = "update cart set state = '$state' where idx = '$val' and mem_id = '$mem_id'";
      $result = sql_query($sql);
    }
  } else {
    $sql = "update cart set state = '$state' where idx = '$cart_idx' and mem_id = '$mem_id'";
    $result = sql_query($sql);
  }

  if($result) {
    sql_query("commit");
    $data = array('result' => 'success');
  } else {
    sql_query("rollback");
    throw new Exception('잠시후 다시 시도해주세요.');
  }

  echo json_encode($data);
} catch (Exception $e) {
  $data = array('result' => 'fail');
  $data['msg'] = $e;
  echo json_encode($data);
}

?>