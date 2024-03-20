<?php
require_once "./_common.php";

$order_num = $_POST['order_num'];
$priv = $_POST['priv'];
$manager = $_POST['manager'];

$data = array(
  'status' => '',
);

try{
  // // 관리자권한 확인
  if($priv >= 9){
    $sql = "UPDATE `order` SET state = 'P', `manager` = '{$manager}' WHERE order_num = '{$order_num}'";
    $result = sql_query($sql);

    if($result){
      $data['status'] = 'success';
    }else{
      $data['status'] = 'error';
    }

  }else{
    $data['status'] = 'error';
  }

  echo json_encode($data, JSON_UNESCAPED_UNICODE);
}catch(Exception $e){
  $data['status'] = 'error';
  $data['msg'] = $e->getMessage();
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>