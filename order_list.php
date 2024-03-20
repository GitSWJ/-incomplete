<?php
require_once('./_common.php');
require_once G5_PATH . '/head.php';

if (!$is_member) {
  alert('회원만 접근 가능합니다.');
  return;
}

?>
<div class="inner">
  <h2 class="sound_only">주문리스트</h2>
  <div class="latest_top_wr">
    <table class="table" style="100%">
      <thead>
        <tr>
          <th>주문번호</th>
          <th>분류</th>
          <th>상담담당자</th>
          <th>작성일</th>
          <th>상태</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $.ajax({
      url: './order_list_ajax.php',
      type: 'post',
      data: {
        mem_id: '<?=$member['mb_id']?>',
        priv : '<?=$member['mb_level']?>',
        view : 'list'
      },
      dataType: 'json',
      success: function(data) {
        if(data.length == 0) {
          alert('주문리스트에 담긴 상품이 없습니다.');
          location.href = './product_order.php';
        }

        var html = '';
        for (var key in data) {

          if(data[key].manager == null) {
            data[key].manager = '미정';
          }

          html += '<tr onclick="location.href=\'./order_view.php?order_num=' + data[key].order_num + '\'">';
          html += '<td>' + data[key].order_num + '</td>';
          html += '<td>' + data[key].order_cate + '</td>';
          html += '<td>' + data[key].manager + '</td>';
          html += '<td>' + data[key].reg_date + '</td>';
          html += '<td>' + data[key].state + '</td>';
          html += '</tr>';
        }

        $('.table tbody').html(html);
      }
    });
  });

</script>
<style>
  .table {
    width: 100%;
  }
  .table thead th {
    text-align: center;
  }
  .table tbody td {
    text-align: center;
  }

  .table tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
  }
  .table tbody tr:hover {
    background-color: #f0f0f0;
  }

</style>
<? require_once G5_PATH . '/tail.php';