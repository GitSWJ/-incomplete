<?php
  include_once('./_common.php');

  try{
  // product 테이블에서 모든 데이터를 가져옴
  $sql = "SELECT * FROM product";
  $result = sql_query($sql);

  // 결과가 있는지 확인
  if (sql_num_rows($result) > 0) {
    // 결과가 있을 경우 행을 배열로 가져옴
    $data = sql_fetch_array($result);
  } else {
    // 결과가 없을 경우
    $data = 0;
  }

  if($data == 0) {
    $msg = "등록된 제품이 없습니다.";
  } else {
    // 외경, 두께, 재고를 배열로 가져옴
    $sql = "SELECT diameter, thickness, SUM(stock) AS total_stock FROM product GROUP BY diameter, thickness";
    $result = sql_query($sql);
    $data = array();
    while ($row = sql_fetch_array($result)) {
      $data[] = $row;
    }
  }
  
  $json = json_encode($data);

  }catch (Exception $e) {
    $json = json_encode($e);
  }
  echo $json;
?>