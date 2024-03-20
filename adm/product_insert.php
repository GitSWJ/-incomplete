<?php
include_once('./_common.php');

// POST로 넘어온 값이 없을 경우
if (!$_POST) {
    // 제품등록 페이지로 이동
    header('Location: ./product_form.php');
    exit;
}

// tranjection 시작
sql_query("begin");

try{

// POST로 넘어온 값이 있을 경우
// 제품 테이블에 데이터를 넣음
$sql = "INSERT INTO product (diameter, thickness, material, stock, method) VALUES ('{$_POST['diameter']}', '{$_POST['thickness']}', '{$_POST['material']}', '{$_POST['stock']}' , '{$_POST['method']}')";
$result = sql_query($sql);

if ($result) {
  // 결과가 있을 경우
  sql_query("commit");
  $data = array('result' => 'success');
} else {
  // 결과가 없을 경우
  sql_query("rollback");
  $data = array('result' => 'fail');
}

} catch (Exception $e) {
  // 예외가 발생할 경우
  sql_query("rollback");
  // 에러메시지 출력
  alert($e->getMessage());
}

// 제품관리 페이지로 이동
header('Location: ./product.php');
?>