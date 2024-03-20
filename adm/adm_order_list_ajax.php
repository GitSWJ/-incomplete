<?php
require_once "./_common.php";

$mem_id = $_POST['mem_id'];
$priv = $_POST['priv'];
$view = $_POST['view'];

try{

  // 관리자권한 확인
  if($priv >= '9') {
    // view 값이 view일 경우 해당 주문번호에 해당하는 데이터만 가져온다.
    if($view == 'view') {
      $sql = "select * from `order` where order_num = '{$_POST['order_num']}'";
      $result = sql_query($sql);
      $data = array();

      while($row = sql_fetch_array($result)) {
        $sql = "select * from `cart` where idx = '{$row['cart_idx']}'";
        $cart = sql_fetch($sql);
        $data[$row['idx']]['order'] = $row;
        $data[$row['idx']]['priv'] = $priv;

        if($data[$row['idx']]['order']['state'] == 'W') {
          $data[$row['idx']]['order']['state'] = '대기중';
        } else if ($data[$row['idx']]['order']['state'] == 'D') {
          $data[$row['idx']]['order']['state'] = '완료';
        } else if ($data[$row['idx']]['order']['state'] == 'P') {
          $data[$row['idx']]['order']['state'] = '진행중';
        }

        if($data[$row['idx']]['order']['order_cate'] == 'C') {
          $data[$row['idx']]['order']['order_cate'] = '상용제품';
        } else if ($data[$row['idx']]['order']['order_cate'] == 'P') {
          $data[$row['idx']]['order']['order_cate'] = '가공';
        } else if ($data[$row['idx']]['order']['order_cate'] == 'J') {
          $data[$row['idx']]['order']['order_cate'] = '조관';
        }

        $data[$row['idx']]['cart'] = $cart;

        if($data[$row['idx']]['cart']['option'] == 'N') {
          $data[$row['idx']]['cart']['option'] = '선택안함';
        } else if ($data[$row['idx']]['cart']['option'] == 'C') {
          $data[$row['idx']]['cart']['option'] = '절단';
        } else if ($data[$row['idx']]['cart']['option'] == 'B') {
          $data[$row['idx']]['cart']['option'] = '절곡';
        } else if ($data[$row['idx']]['cart']['option'] == 'T') {
          $data[$row['idx']]['cart']['option'] = '면치';
        } else if ($data[$row['idx']]['cart']['option'] == 'W') {
          $data[$row['idx']]['cart']['option'] = '용접';
        }

        // 상품정보 가져오기
        $product_sql = "select * from `product` where idx = '{$cart['product_idx']}'";
        $product = sql_fetch($product_sql);
        $data[$row['idx']]['product'] = $product;

        if($data[$row['idx']]['product']['method'] == '') {
          $data[$row['idx']]['product']['method'] = '-';
        }

        // 회원정보 가져오기
        $member_sql = "select mb_name from `t_passmember` where mb_id = '{$row['mem_id']}'";
        $member = sql_fetch($member_sql);
        $data[$row['idx']]['member'] = $member;
      }
      echo json_encode($data);
      return;
    }

    // view 값이 list일 경우 모든 주문번호에 해당하는 데이터를 가져온다.
    $sql = "select * from `order` group by order_num order by idx desc";
    $result = sql_query($sql);
    $data = array();
    $fidx = sql_num_rows($result)+1;

    while($row = sql_fetch_array($result)) {
      $fidx = $fidx - 1;
      $row['fidx'] = $fidx;

      if($row['state'] == 'W') {
        $row['state'] = '대기중';
      } else if($row['state'] == 'D') {
        $row['state'] = '완료';
      } else if($row['state'] == 'P') {
        $row['state'] = '진행중';
      }

      if($row['order_cate'] == 'C') {
        $row['order_cate'] = '상용제품';
      } else if($row['order_cate'] == 'P') {
        $row['order_cate'] = '가공';
      } else if($row['order_cate'] == 'J') {
        $row['order_cate'] = '조관';
      }

      $data[] = $row;

    }
    echo json_encode($data);
    return;

  } else {
    throw new Exception('권한이 없습니다.');
  }
  
  echo json_encode($data);

} catch (Exception $e) {
  $data = array('result' => 'fail');
  $data['msg'] = $e;
  echo json_encode($data);
}