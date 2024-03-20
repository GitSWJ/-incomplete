<?php
include_once('./_common.php');
require_once './admin.head.php';

if (!$is_member) {
  alert('회원만 접근 가능합니다.');
  return;
}

?>
<div class="inner">
  <h2 class="sound_only">주문리스트</h2>
  <div class="latest_top_wr">
    <table class="table" id="table1">
      <thead>
        <tr>
          <th>상품정보</th>
          <th>수량</th>
          <th>예상 단가</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <div class="result">
    <table class="table">
      <tr>
        <td>주문 금액</td>
        <td>회원할인</td>
        <td>예상 합계</td>
      </tr>
      <tr>
        <td id="order_price">0</td>
        <td id="order_discount">0</td>
        <td id="order_final_price">0</td>
      </tr>
  </div>

  <div class="info">
    <h2>상담 정보</h2>
    <hr>
    <table class="table" id="table2">
      
    </table>
  </div>
  <div>
    <button type="button" class="btn btn-primary" id="list">목록</button>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $.ajax({
      url: './adm_order_list_ajax.php',
      type: 'post',
      data: {
        mem_id: '<?=$member['mb_id']?>',
        priv : '<?=$member['mb_level']?>',
        view : 'view',
        order_num : "<?=$_GET['order_num']?>"
      },
      dataType: 'json',
      success: function(data) {
        if(data.length == 0) {
          alert('주문리스트에 담긴 상품이 없습니다.');
          location.href = './product_order.php';
        }
        console.log(data);
        var table1 = '';
        var table2 = '';
        var price = 0;
        var discount = 0;
        var final_price = 0;

        for (var key in data) {
          // 예상단가 계산
          var ex_price = data[key].cart.qty*data[key].cart.unit_price;
          ex_price = ex_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원';

          // 담당자값이 없을 경우 미정으로 표시
          if(data[key].order.manager == null) {
            data[key].order.manager = '미정';
          }

          table1 += '<tr>';
          table1 += '<td>';
          table1 += '생산방식 : ' + data[key].product.method + '</br>';
          table1 += '외경 : ' + data[key].product.diameter + '</br>';
          table1 += '두께 : ' + data[key].product.thickness + '</br>';
          table1 += '단위길이 : ' + data[key].cart.unit_length + '</br>';
          table1 += '소재 : ' + data[key].product.material + '</br>';
          table1 += '옵션 : ' + data[key].cart.option + '</td>';
          table1 += '<td>' + data[key].cart.qty + '</td>';
          table1 += '<td>' + ex_price + '</td>';
          table1 += '</tr>';

          price += data[key].cart.unit_price * data[key].cart.qty;
        }

        table2 += '<tr>';
        table2 += '<th>상태</th>';
        table2 += '<td>' + data[key].order.state + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>분류</th>';
        table2 += '<td>' + data[key].order.order_cate + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>작성일</th>';
        table2 += '<td>' + data[key].order.reg_date + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>상담담당자</th>';
        // 로그인한 회원이 관리자일 경우만 담당자를 변경할 수 있도록 함
        if(data[key].priv >= 9) {
          table2 += '<td>'
          table2 += '<input type="text" id="manager" value="' + data[key].order.manager + '">';
          table2 += '<button type="button" id="manager_change">변경</button>';
          table2 += '</td>';
        } else {
          table2 += '<td>' + data[key].order.manager + '</td>';
        }
        table2 += '</tr>';
        table2 += '<hr>';
        table2 += '<tr>';
        table2 += '<th>작성자</th>';
        table2 += '<td>' + data[key].member.mb_name + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>연락처</th>';
        table2 += '<td>' + data[key].order.tel + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>휴대전화</th>';
        table2 += '<td>' + data[key].order.phone + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>이메일</th>';
        table2 += '<td>' + data[key].order.email + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>상호명</th>';
        table2 += '<td>' + data[key].order.company + '</td>';
        table2 += '</tr>';
        table2 += '<tr>';
        table2 += '<th>추가 전달사항</th>';
        table2 += '<td>' + data[key].order.memo + '</td>';
        table2 += '</tr>';

        $('#table1 tbody').html(table1);
        $('#table2').html(table2);

        discount = price * 0.1; // 회원 할인
        final_price = price - discount;

        price = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원';
        discount = discount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원';
        final_price = final_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원'; 
        
        $('#order_price').text(price);
        $('#order_discount').text(discount);
        $('#order_final_price').text(final_price);
      }
    });
  });

  // 담당자 변경
  $(document).on('click', '#manager_change', function() {
    var manager = $('#manager').val();
    $.ajax({
      url: './adm_order_manager_ajax.php',
      type: 'post',
      data: {
        order_num: "<?=$_GET['order_num']?>",
        manager: manager,
        priv: <?=$member['mb_level']?>
      },
      dataType: 'json',
      success: function(data) {
        if(data.status == 'success') {
          alert('변경이 완료되었습니다.');
          location.reload();
        } else {
          console.log(data);
          alert('잠시 후 다시 시도해 주세요.');
        }
      }
    });
  });

  

  $('#list').click(function() {
    location.href = './adm_order_list.php';
  });

</script>
<style>
  .table {
    width: 100%;
    border-collapse: collapse;
  }
  .table thead th {
    border: 1px solid #000;
    padding: 10px;
  }
  .table tbody td {
    border: 1px solid #000;
    padding: 10px;
  }
  .result {
    margin-top: 20px;
    text-align: center;
  }
  .result td {
    border: 1px solid #000;
    padding: 10px;
  }
  .info {
    margin-top: 20px;
  }
  .info th {
    width: 20%;
    border: 1px solid #000;
    padding: 10px;
  }
  .info td {
    border: 1px solid #000;
    padding: 10px;
  }
  .btn {
    margin-top: 20px;
    float: right;
  }


</style>
<? require_once G5_PATH . '/tail.php';