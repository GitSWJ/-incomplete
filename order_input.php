<?php
require_once('./_common.php');
require_once G5_PATH . '/head.php';

if (!$is_member) {
  alert('회원만 접근 가능합니다.');
  return;
}

?>
<div class="inner">
  <h2 class="sound_only">의뢰 및 상담</h2>
  <div class="latest_top_wr">
    <h2>의뢰 및 상담</h2>
    <table class="cart_table" style="100%">
      <thead>
        <tr>
          <th>상품정보</th>
          <th>수량</th>
          <th>금액</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <div class="input">
    <div class="left">
      <h2>정보입력</h2>
      <p>분류</p>
      <input type="radio" name="category" value="N">개인
      <input type="radio" name="category" value="E">기업체
      <p>상호명</p>
      <input type="text" name="company">
      <p>담당자명<span style="color:red">*</span></p>
      <input type="text" name="name">
      <p>연락처</p>
      <input type="text" name="tel">
      <p>휴대전화<span style="color:red">*</span></p>
      <input type="text" name="phone">
      <p>이메일<span style="color:red">*</span></p>
      <input type="text" name="email"> @ <input type="text" name="email_domain">
      <select name="email_select">
        <option value="N">직접입력</option>
        <option value="G">google.com</option>
        <option value="N">naver.com</option>
      </select>
      <p>추가전달사항</p>
      <textarea name="memo"></textarea>
    </div>
    <div class="right">
      <p>예상 금액</p>
      <p type="text" name="price">-</p>
      <p>회원 할인</p>
      <p type="text" name="discount">-</p>
      <hr>
      <p>예상 합계</p>
      <p style="font-size: 10px; font-weight:color: #000;">*정확한 금액은 의뢰 및 상담을 통해 전달드립니다.</p></p>
      <p style="font-size: 30px; bold" type="text" name="final_price">-</p>

      <div class="privacy">
        <p>개인정보 수집.이용에 대한 안내(필수 수집.이용 항목)</p>
        <p>- 수집항목 : 담당자명, 연락처, 이메일, 상호면</p>
        <p>- 수집목적 : 문의 및 상담 요청에 대한 회신</p>
      </div>
      <br>
      <input type="checkbox" name="privacy_check" style="width: 20px; height: 20px;">개인정보 수집 및 이용에 동의합니다.

      <button type="button" class="btn btn-primary" id="order">의뢰 및 상담</button>
      <br>
      <button type="button" class="btn btn-danger" id="cancel">취소</button>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    var cart_idx = "";
    cart_idx += document.cookie.split(';').find(function(c) {
      return c.trim().startsWith('cart_idx=');
    }).split('=')[1].split(',');
    $.ajax({
      url: './order_cart_ajax.php',
      type: 'post',
      data: {
        mem_id: '<?=$member['mb_id']?>',
        state : 'order_input',
        cart_idx: cart_idx
      },
      dataType: 'json',
      success: function(data) {
        if(data.length == 0) {
          alert('장바구니에 담긴 상품이 없습니다.');
          location.href = './product_order.php';
        }
        var html = '';
        for (var key in data) {
          var price = data[key]['cart']['unit_price'] * data[key]['cart']['qty'];
          price = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
          price += '원';

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
        $('.cart_table tbody').html(html);

        // 예상 금액, 회원 할인, 예상 합계 계산
        var price = 0;
        var discount = 0;
        var final_price = 0;
        for (var key in data) {
          price += data[key]['cart']['unit_price'] * data[key]['cart']['qty'];
        }
        discount = price * 0.1;
        final_price = price - discount;

        // 숫자 포맷 변경 (3자리마다 콤마)
        price = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        discount = discount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        final_price = final_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        price += '원';
        discount += '원';
        final_price += '원';

        $('p[name="price"]').text(price);
        $('p[name="discount"]').text(discount);
        $('p[name="final_price"]').text(final_price);

      }
    });
  });
  
  // 이메일 선택 시
  $('select[name="email_select"]').on('change', function() {
    if($(this).val() == 'N') {
      input[name="email_domain"].val('naver.com');
    } else if($(this).val() == 'G') {
      input[name="email_domain"].val('google.com');
    } else {
    }
  });

  // 의뢰 및 상담 버튼 클릭 시
  $('#order').on('click', function() {
    var category = $('input[name="category"]:checked').val();
    var company = $('input[name="company"]').val();
    var name = $('input[name="name"]').val();
    var tel = $('input[name="tel"]').val();
    var phone = $('input[name="phone"]').val();
    var email = $('input[name="email"]').val() + '@' + $('input[name="email_domain"]').val();
    var email_select = $('select[name="email_select"]').val();
    var memo = $('textarea[name="memo"]').val();
    var cart_idx = "";
    cart_idx += document.cookie.split(';').find(function(c) {
      return c.trim().startsWith('cart_idx=');
    }).split('=')[1].split(',');

    if($('input[name="privacy_check"]').is(':checked') == false) {
      alert('개인정보 수집 및 이용에 동의해주세요.');
      return;
    }

    if(category == undefined) {
      alert('분류를 선택해주세요.');
      return;
    }
    if(name == '') {
      alert('담당자명을 입력해주세요.');
      return;
    }
    if(phone == '') {
      alert('휴대전화를 입력해주세요.');
      return;
    }
    if(email == '') {
      alert('이메일을 입력해주세요.');
      return;
    }

    $.ajax({
      url: './order_input_ajax.php',
      type: 'post',
      data: {
        mem_id: '<?=$member['mb_id']?>',
        category: category,
        company: company,
        name: name,
        tel: tel,
        phone: phone,
        email: email,
        memo: memo,
        cart_idx: cart_idx
      },
      dataType: 'json',
      success: function(data) {
        if(data.result == 'success') {
          alert('의뢰 및 상담이 완료되었습니다.');
          location.href = './order_list.php';
        } else {
          alert('의뢰 및 상담에 실패했습니다.');
        }
      }
    });
  });

</script>
<style>
  .inner {
    width: 100%;
    height: 100%;
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

  .input {
    width: 100%;
    padding: 20px;
  }
  .input p {
    margin: 10px 0;
  }
  .input input {
    width: 90%;
    height: 30px;
  }
  .input textarea {
    width: 90%;
    height: 100px;
  }
  .input select {
    width: 90px;
    height: 30px;
  }
  .input input[type="radio"] {
    width: 20px;
    height: 20px;
  }
  .input input[type="radio"]:checked {
    background-color: #000;
  }

  .input input[name="email"] {
    width: 150px;
  }
  .input input[name="email_domain"] {
    width: 150px;
  }
  .input .left {
    width: 50%;
    height: 600px;
    float: left;
  }
  .input .right {
    width: 50%;
    height: 600px;
    float: right;
    background-color: #f5f5f5;
  }

  .input .right p {
    text-align: right;
  }

  .privacy {
    width: 100%;
    height: 100px;
    background-color: lightblue;
    padding: 10px;
  }
  .privacy_check {
    width: 20px;
    height: 20px;
  }

  .btn {
    width: 100%;
    height: 30px;
    margin: 10px 0;
  }
</style>
<? require_once G5_PATH . '/tail.php';