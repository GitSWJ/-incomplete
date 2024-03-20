<?php
require_once "common.php";

$diameter = $_POST['diameter'];
$thickness = $_POST['thickness'];
$check = $_POST['check'];
$length = isset($_POST['length']) ? $_POST['length'] : 1;
$material = isset($_POST['material']) ? $_POST['material'] : '';

try{
  if($check == 'material') {
      $sql = "SELECT material,method FROM product WHERE diameter = '{$diameter}' AND thickness = '{$thickness}'";
      $result = sql_query($sql);
      $data = array();
      while ($row = sql_fetch_array($result)) {
        $data['material'][] = $row['material'];
        $data['method'][] = $row['method'];
      }

      echo json_encode($data);
  } else {
    
    if($material == '' || $material == 'undefined') {
      echo json_encode(array('stock' => '소재를 선택해주세요', 'unit_price' => null));
      return;
    }
      $sql = "SELECT idx,stock,unit_price,method FROM product WHERE diameter = '{$diameter}' AND thickness = '{$thickness}' AND material = '{$material}'";
      $result = sql_query($sql);
      $data = array();

      while ($row = sql_fetch_array($result)) {
        $data['idx'] = $row['idx'];
        $data['method'] = $row['method'];
        $data['stock'] = floor($row['stock'] / $length);
        if($row['unit_price'] == 0) {
          $data['unit_price'] = null;
        } else {
          $data['unit_price'] = $row['unit_price']*$length;
        }
      }

    echo json_encode($data);
    }

  }catch (Exception $e) {
    echo json_encode($e);
  }
?>