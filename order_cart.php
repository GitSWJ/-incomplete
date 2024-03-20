<?php
require_once('./_common.php');
require_once G5_PATH . '/head.php';

if (!$is_member) {
  alert('회원만 접근 가능합니다.');
  return;
}

?>
<div class="inner">
  <h2 class="sound_only">장바구니</h2>
  <div class="latest_top_wr">
    <h2>장바구니</h2>
    <table class="cart_table" style="100%">
      <thead>
        <tr>
          <th style="width: 10%;">
          모두선택<input type="checkbox" name="all_check" style="width: 100%; height: 100%;">
          </th>
          <th>상품정보</th>
          <th>수량</th>
          <th>금액</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
        <tr>
          <!-- 선택한 상품의 금액 -->
          <td colspan="3">총 금액</td>
          <td colspan="2" id="total_price">0</td>
        </tr>
        <tr>
          <td colspan="5">
            <button type="button" class="btn btn-primary" id="shopping">상용제품 더보기</button>
            <button type="button" class="btn btn-danger" id="order">의뢰 및 상담</button>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $.ajax({
      url: './order_cart_ajax.php',
      type: 'post',
      data: {
        mem_id: '<?=$member['mb_id']?>',
        state : 'order_cart'
      },
      dataType: 'json',
      success: function(data) {
        if(data.length == 0) {
          alert('장바구니에 담긴 상품이 없습니다.');
          location.href = './product_order.php';
        }

        var html = '';
        for (var key in data) {
          if(data[key]['cart']['unit_price'] == '-') {
            data[key]['cart']['unit_price'] = '미정';
          }else{
            var price = data[key]['cart']['unit_price']*data[key]['cart']['qty'];
            price = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원';
          }
          if(data[key]['cart']['option'] == 'N') {
            data[key]['cart']['option'] = '선택안함';
          } else if(data[key]['cart']['option'] == 'C') {
            data[key]['cart']['option'] = '절단';
          } else if(data[key]['cart']['option'] == 'B') {
            data[key]['cart']['option'] = '절곡';
          } else if(data[key]['cart']['option'] == 'T') {
            data[key]['cart']['option'] = '면치';
          } else if(data[key]['cart']['option'] == 'W') {
            data[key]['cart']['option'] = '용접';
          }
          html += '<tr>';
          html += '<td><input type="checkbox" name="cart_idx[]" value="' + data[key]['cart']['idx'] + '" style="width: 100%; height: 100%;"></td>';
          html += '<td>생산방식 : ';
          html += data[key]['product']['method'];
          html += '</br>';
          html += '외경 : ';
          html += data[key]['product']['diameter'];
          html += '</br>';
          html += '두께 : ';
          html += data[key]['product']['thickness'];
          html += '</br>';
          html += '단위길이 : ';
          html += data[key]['cart']['unit_length'];
          html += '</br>';
          html += '소재 : ';
          html += data[key]['product']['material'];
          html += '</br>';
          html += '옵션 : ';
          html += data[key]['cart']['option'];
          html += '</td>';
          html += '<td>';
          html += data[key]['cart']['qty'];
          html += '</td>';
          html += '<td>';
          html += price;
          html += '</td>';
          html += '</tr>';
        }
        html += '<tr>';
        html += '<td colspan="5">';
        html += '<button type="button" name="delete_cart" class="del_btn">선택삭제</button>';
        html += '</td>';
        $('.cart_table tbody').html(html);
      }
    });
  });

  // 선택한 상품의 금액을 계산
  $(document).on('change', 'input[name="cart_idx[]"]', function() {
    var total_price = 0;
    $('input[name="cart_idx[]"]:checked').each(function() {
      var idx = $(this).val();
      var price_text = $(this).parent().next().next().next().text();
      price_text = price_text.replace(/,/g, ''); // 쉼표 제거
      var price = parseInt(price_text.replace('원', '')); // '원' 제거 후 숫자로 변환
      total_price += price; // 총 가격에 가격 추가
    });
    total_price = total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원';
    $('#total_price').text(total_price);
  });

  // 삭제버튼
  $(document).on('click', 'button[name="delete_cart"]', function() {
    if($('input[name="cart_idx[]"]:checked').length == 0) {
      alert('상품을 선택해주세요.');
      return;
    }

    if(confirm('선택한 상품을 삭제하시겠습니까?')) {
      var cart_idx = [];
      $('input[name="cart_idx[]"]:checked').each(function() {
        cart_idx.push($(this).val());
      });

      $.ajax({
        url: './order_cart_state_ajax.php',
        type: 'post',
        data: {
          mem_id: '<?=$member['mb_id']?>',
          cart_idx: cart_idx,
          state: 'N'
        },
        dataType: 'json',
        success: function(data) {
          if(data.result == 'success') {
            alert('삭제되었습니다.');
            location.reload();
          } else {
            alert('잠시후 다시 시도해주세요.');
          }
        }
      });
    }
  });
  

  // 모두선택 버튼
  $(document).on('change', 'input[name="all_check"]', function() {
    if($(this).is(':checked')) {
      $('input[name="cart_idx[]"]').prop('checked', true);
      // 모든 상품의 금액을 계산
      var total_price = 0;
      $('input[name="cart_idx[]"]:checked').each(function() {
        var price_text = $(this).parent().next().next().next().text();
        price_text = price_text.replace(/,/g, ''); // 쉼표 제거
        var price = parseInt(price_text.replace('원', '')); // '원' 제거 후 숫자로 변환
        total_price += price; // 총 가격에 가격 추가
      });
      total_price = total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원';
      $('#total_price').text(total_price);
    } else {
      $('input[name="cart_idx[]"]').prop('checked', false);
      $('#total_price').text(0);
    }
  });

  // 상용제품 더보기 버튼
  $(document).on('click', '#shopping', function() {
    location.href = './product_order.php';
  });

  // 의뢰및 상담 버튼
  $(document).on('click', '#order', function() {
    // 체크된 상품이 없을경우
    if($('input[name="cart_idx[]"]:checked').length == 0) {
      alert('상품을 선택해주세요.');
      return;
    }

    // 선택한 상품의 idx를 배열로 만들어서 보낸다.
    var cart_idx = [];
    $('input[name="cart_idx[]"]:checked').each(function() {
      cart_idx.push($(this).val());
    });

    // 선택한 상품의 idx를 쿠키에 저장
    document.cookie = 'cart_idx=' + cart_idx;

    // 정보입력 페이지로 이동
    location.href = './order_input.php';

  });

</script>
<style>
  .inner {
    width: 100%;
    height: 100%;
    background-color: #f5f5f5;
  }

  .cart_table {
    width: 100%;
    border-collapse: collapse;
  }
  .cart_table th, .cart_table td {
    border: 1px solid #000;
    padding: 10px;
  }
  .cart_table th {
    background-color: #000;
    color: #fff;
  }
  .cart_table tbody tr:nth-child(odd) {
    background-color: #fff;
  }
  .cart_table tbody tr:nth-child(even) {
    background-color: #f5f5f5;
  }
  .cart_table tfoot tr {
    background-color: #000;
    color: #fff;
  }
  .cart_table tfoot tr td {
    padding: 10px;
  }
  .cart_table tfoot tr td button {
    width: 49.5%;
    height: 100%;
    border: none;
    color: #000;
    background-color: #fff;
  }
  .cart_table tfoot tr td button:hover {
    background-color: #000;
    color: #fff;
  }

  .del_btn {
    width: 10%;
    height: 100%;
    border: none;
    color: #ffffff;
    background-color: #111111;
  }

</style>
<? require_once G5_PATH . '/tail.php';