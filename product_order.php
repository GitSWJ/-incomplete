<?php
require_once('./_common.php');
require_once G5_PATH . '/head.php';

// 외경과 두께에 해당하는 재고 수량을 가져오는 쿼리
$sql = "SELECT diameter, thickness, SUM(stock) AS total_stock FROM product GROUP BY diameter, thickness";
$result = sql_query($sql);

$sql = "SELECT diameter FROM product GROUP BY diameter ORDER BY diameter";
$col_result = sql_query($sql);
$col_cnt = sql_num_rows($col_result);

$sql = "SELECT thickness FROM product GROUP BY thickness ORDER BY thickness";
$row_result = sql_query($sql);
$row_cnt = sql_num_rows($row_result);

// 결과를 배열로 변환
$data = array();
while ($row = sql_fetch_array($result)) {
    $data[$row['diameter']][$row['thickness']] = $row['total_stock'];
}
?>
<div class="inner">
  <h1>재고 현황</h1>
  <table class="stock_table">
    <tr>
        <th colspan="2">외경(mm)</th>
        <?php while ($col_row = sql_fetch_array($col_result)) { ?>
            <th width="100px"><?= $col_row['diameter'] ?></th>
        <?php } ?>
    </tr>
    <tr>
        <th rowspan="<?= $row_cnt ?>">두께(mm)</th>
        <?php while ($row_row = sql_fetch_array($row_result)) { ?>
            <td><?= $row_row['thickness'] ?></td>
            <?php foreach ($data as $diameter => $thickness) { ?>
              <?php if (isset($thickness[$row_row['thickness']])) { ?>
                <td 
                style="background-color: <?= $thickness[$row_row['thickness']] >= 15000 ? '#004FE0' : ($thickness[$row_row['thickness']] >= 10000 ? '#52A4DC' : ($thickness[$row_row['thickness']] >= 5000 ? 'lightblue' : '')) ?>"
                id="stock_<?= $diameter ?>_<?= $row_row['thickness'] ?>"
                >
                  <!-- <?= $thickness[$row_row['thickness']] ?> -->
                </td>
              <?php } else { ?>
                <!-- 재고 수량이 없을 때 빈 값을 출력 -->
                <td></td>
              <?php } ?>
            <?php } ?>
        </tr>
    <?php } ?>
</table>

</div>
<div id="modal" class="modal">
  <div class="modal_content">
    <span class="close">&times;</span>
    <div class="modal_inner">
      <input type="hidden" id="product_idx" name="product_idx">
      <p>재고 상세 정보</p>
      <p>생산방식</p>
      <p name="method"></p>
      <p>외경</p>
      <p name="diameter"></p>
      <p>두께</p>
      <p name="thickness"></p>
      <p>단위길이</p>
      <input type="number" id="unit_length" name="unit_length" placeholder="단위길이를 입력해주세요.(m단위)">
    </div>

    <p>소재</p>
    <div id="material_select">
      <select name="material" id="material">
      </select>
    </div>

    <p>재고 수량</p>
    <p id="stock">소재와 길이를 선택해주세요.</p>
    </br>
    <p>주문 수량</p>
    <input type="number" id="order_qty" name="order_qty">

    <p>옵션</p>
    <div id="option_select">
      <input type="radio" name="option" id="option1" value="N" default checked>선택안함
      <input type="radio" name="option" id="option1" value="C">절단
      <input type="radio" name="option" id="option2" value="V">절곡
      <input type="radio" name="option" id="option3" value="I">면치
      <input type="radio" name="option" id="option3" value="W">용접
    </div>

    <p>예상단가</p>
    <p id="unit_price">0원</p>

    <div class="modal_btn">
      <button type="button" id="cart_btn">장바구니 담기</button>
      <button type="button" id="request_btn">의뢰 및 상담</button>
      <button type="button" id="cancel_btn">취소</button>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {

    $('#modal').css('display', 'none');
    $('#order_qty').attr('disabled', true);

    // modal 창을 닫는 이벤트를 발생시키는 함수
    $('.close').click(function() {
      $('#modal').css('display', 'none');
      $('#material_select select').empty();
      $('#stock').text('');
      $('#order_qty').val('');
      $('#unit_length').val('');
      $('#product_idx').val('');
      $('#unit_price').text('0원');
      $('#order_qty').attr('disabled', true);
    });

    // 취소 버튼을 클릭했을 때 modal 창을 닫는 이벤트를 발생시키는 함수
    $('#cancel_btn').click(function() {
      $('#modal').css('display', 'none');
      $('#material_select select').empty();
      $('#stock').text('');
      $('#order_qty').val('');
      $('#unit_length').val('');
      $('#product_idx').val('');
      $('#unit_price').text('0원');
      $('#order_qty').attr('disabled', true);
    });
  });

  // 재고 수량을 클릭했을 때 이벤트를 발생시키는 함수
  $('.stock_table td').click(function() {

    var id = $(this).attr('id');

    if(id == undefined){
      alert('재고 수량이 없습니다.');
      return;
    }else{
      var idArr = id.split('_');
      var diameter = idArr[1];
      var thickness = idArr[2];
      // 외경과 두께 표시
      $('#modal .modal_content p[name=diameter]').text(diameter+' mm');
      $('#modal .modal_content p[name=thickness]').text(thickness+' mm');

      $.ajax({
        url: 'product_stock_check.php',
        type: 'post',
        dataType: 'json',
        data: {
          diameter: diameter,
          thickness: thickness,
          check: 'material'
        },
        success: function(data) {
          var method = "";
          for(var i = 0; i < data.method.length; i++){
            method += data.method[i] + ",";
          }
          method = method.trim().substring(0, method.length-1);
          $('#modal .modal_content p[name=method]').text(method);
          $('#modal').css('display', 'block');

          // 소재 표시
          $('#material_select select').empty();
          $('#material_select select').append('<option value="">소재 선택</option>');
          for (var i = 0; i < data.material.length; i++) {
            $('#material_select select').append('<option value="' + data.material[i] + '">' + data.material[i] + '</option>');
          }
        }
      });
    }
  });

  // 소재를 선택했을 때 이벤트를 발생시키는 함수
  $(document).on('change', '#material', function() {
    var material = $(this).val();
    var diameter = $('#modal .modal_content p[name=diameter]').text();
    var thickness = $('#modal .modal_content p[name=thickness]').text();
    var length = $('#unit_length').val();

    $.ajax({
      url: 'product_stock_check.php',
      type: 'post',
      dataType: 'json',
      data: {
        diameter: diameter,
        thickness: thickness,
        material: material,
        length: length,
        check: 'stock'
      },
      success: function(data) {
        // 외경과 두께에 해당하는 소재의 재고 수량을 가져와서 출력
        $('#modal .modal_content p[name=method]').text(data.method);
        $('#product_idx').val(data.idx);
        $('#stock').text(data.stock);
        $('#order_qty').val(0);
        $('#order_qty').focus();
        $('#order_qty').attr('max', data.stock);
        $('#order_qty').attr('disabled', false);
        if(data.unit_price == null){
          $('#unit_price').text('상담 후 결정');
        }else{
          data.unit_price = data.unit_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
          $('#unit_price').text("단위 당"+data.unit_price + '원');
        }
      }
    });
  });

  $(document).on('change', '#unit_length', function() {
    var material = $('#material').val();
    var diameter = $('#modal .modal_content p[name=diameter]').text();
    var thickness = $('#modal .modal_content p[name=thickness]').text();
    var length = $('#unit_length').val();

    $.ajax({
      url: 'product_stock_check.php',
      type: 'post',
      dataType: 'json',
      data: {
        diameter: diameter,
        thickness: thickness,
        material: material,
        length: length,
        check: 'stock'
      },
      success: function(data) {
        // 외경과 두께에 해당하는 소재의 재고 수량을 가져와서 출력
        $('#stock').text(data.stock);
        $('#order_qty').val(0);
        $('#order_qty').focus();
        $('#order_qty').attr('max', data.stock);
        $('#order_qty').attr('disabled', false);
        if(data.unit_price == null){
          $('#unit_price').text('상담 후 결정');
        }else{
          $('#unit_price').text("단위 당"+data.unit_price + '원');
        }
      }
    });
  });

  $(document).on('change', '#order_qty', function() {
    // 앞뒤 공백과 0을 제거하고 주문 수량을 가져옴
    var order_qty = $(this).val().trim();
    var order_qty = parseInt(order_qty);

    // 주문 수량이 0보다 작거나 같을 때
    if (order_qty <= 0) {
      alert('주문 수량은 0보다 커야 합니다.');
      $(this).val(0);
      $(this).focus();
    }

    // 주문 수량이 재고 수량보다 클 때
    if (order_qty > $(this).attr('max')) {
      alert('주문 수량이 재고 수량보다 많습니다.');
      $(this).val($(this).attr('max'));
      $(this).focus();
    }
  });

  // 장바구니 담기 버튼을 클릭했을 때 이벤트를 발생시키는 함수
  $(document).on('click', '#cart_btn', function() {
    // 로그인 여부를 확인
    if('<?=$member['mb_id']?>' == ''){
      alert('로그인 후 이용해주세요.');
      return;
    }

    var product_idx = $('#product_idx').val();
    var length = $('#unit_length').val();
    var order_qty = $('#order_qty').val();
    var option = $('input:radio[name=option]:checked').val();
    var mem_id = '<?=$member['mb_id']?>';

    if (length == '' || length == 'undefined') {
      alert('단위길이를 입력해주세요.');
      return;
    }
    if (material == '' || material == 'undefined') {
      alert('소재를 선택해주세요.');
      return;
    }
    if (order_qty == '' || order_qty == 'undefined' || order_qty == 0) {
      alert('주문 수량을 입력해주세요.');
      return;
    }
    if (option == '' || option == 'undefined') {
      alert('옵션을 선택해주세요.');
      return;
    }

    $.ajax({
      url: 'order_cart_insert.php',
      type: 'post',
      dataType: 'json',
      data: {
        mem_id: mem_id,
        product_idx: product_idx,
        length: length,
        order_qty: order_qty,
        option: option
      },
      success: function(data) {
        if(data.state == 'success') {
          alert('장바구니에 담겼습니다.');
          location.reload();
        } else {
          alert('장바구니에 담기 실패했습니다.');
        }
      }
    });
  });

  // 의뢰 및 상담 버튼을 클릭했을 때 이벤트를 발생시키는 함수
  $(document).on('click', '#request_btn', function() {
    // 로그인 여부를 확인
    if('<?=$member['mb_id']?>' == ''){
      alert('로그인 후 이용해주세요.');
      return;
    }

    var product_idx = $('#product_idx').val();
    var length = $('#unit_length').val();
    var order_qty = $('#order_qty').val();
    var option = $('input:radio[name=option]:checked').val();
    var mem_id = '<?=$member['mb_id']?>';

    if (length == '' || length == 'undefined') {
      alert('단위길이를 입력해주세요.');
      return;
    }
    if (material == '' || material == 'undefined') {
      alert('소재를 선택해주세요.');
      return;
    }
    if (order_qty == '' || order_qty == 'undefined' || order_qty == 0) {
      alert('주문 수량을 입력해주세요.');
      return;
    }
    if (option == '' || option == 'undefined') {
      alert('옵션을 선택해주세요.');
      return;
    }

    $.ajax({
      url: 'order_cart_insert.php',
      type: 'post',
      dataType: 'json',
      data: {
        mem_id: mem_id,
        product_idx: product_idx,
        length: length,
        order_qty: order_qty,
        option: option,
        pass: "Y"
      },
      success: function(data) {
        if(data.state == 'success') {
          console.log(data);
          // data.idx를 쿠키에 저장하고 의뢰 및 상담 페이지로 이동
          document.cookie = "cart_idx=" + data.idx;
          location.href = './order_input.php';
        } else {
          alert('잠시 후 다시 시도해주세요.');
        }
      }
    });
  });

</script>
<style>
  .stock_table {
    width: 100%;
    border-collapse: collapse;
  }
  .stock_table th, .stock_table td {
    border: 1px solid #000;
    padding: 10px;
  }
  .stock_table th {
    background-color: #f2f2f2;
    width: 100px;
  }
  .stock_table td {
    text-align: center;
    width: 100px;
  }

  .inner {
    width: 1000px;
    margin: 0 auto;
  }

  /* modal 창 스타일 */
  .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.4);
  }
  .modal_content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }
  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }
  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
  .modal_inner {
    margin-bottom: 20px;
  }
  .modal_inner p {
    margin-bottom: 10px;
  }
  .modal_inner p[name=diameter], .modal_inner p[name=thickness] {
    font-size: 20px;
    font-weight: bold;
  }
  #material_select {
    margin-bottom: 20px;
  }
  #material_select select {
    width: 100%;
    padding: 10px;
  }
  #stock {
    font-size: 20px;
    font-weight: bold;
  }
  .modal_btn {
    text-align: right;
  }
  .modal_btn button {
    padding: 10px 20px;
    margin-left: 10px;
  }
  .modal_btn button:nth-child(1) {
    background-color: #004FE0;
    color: #fff;
  }
  .modal_btn button:nth-child(2) {
    background-color: #aaa;
    color: #fff;
  }
  .modal_btn button:hover {
    cursor: pointer;
  }
  .modal_btn button:active {
    transform: translateY(2px);
  }
  .modal_btn button:focus {
    outline: none;
  }
  .order_qty {
    padding: 10px;
    text-align: center;
  }
  #unit_price {
    font-size: 20px;
    font-weight: bold;
  }
  #unit_length {
    width: 100%;
    padding: 10px;
  }
  #option_select {
    margin-bottom: 20px;
  }

  #option_select {
    margin-bottom: 20px;
  }
</style>
<? require_once G5_PATH . '/tail.php';

