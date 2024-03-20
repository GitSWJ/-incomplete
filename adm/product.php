<?php
include_once('./_common.php');
require_once './admin.head.php';

$sql = "SELECT * FROM product ORDER BY idx DESC";
$result = sql_query($sql);

// 결과가 있는지 확인
if (sql_num_rows($result) > 0) {
  // 결과가 있을 경우 행을 배열로 가져옴
  while ($row = sql_fetch_array($result)) {
    $data[] = $row;
  }
} else {
  // 결과가 없을 경우
  $data = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>제품관리</title>
</head>
<body>
  <div>
    <h1>제품관리</h1>
    <button type="button" onclick="location.href='./product_form.php'">제품등록</button>
  </div>
  <div>
    <table class="table1">
      <tr>
        <th>번호</th> 
        <th>외경</th>
        <th>두께</th>
        <th>소재</th>
        <th>생산방식</th>
        <th>재고(M단위)</th>
        <th>수정</th>
      </tr>
      <?php if ($data == 0) { ?>
        <tr>
          <td colspan="6">등록된 제품이 없습니다.</td>
        </tr>
      <?php } else { ?>
          <?php foreach ($data as $row) { ?>
            <tr>
              <td><?=$row['idx']?></td>
              <td><?=$row['diameter']?></td>
              <td><?=$row['thickness']?></td>
              <td><?=$row['material']?></td>
              <td><?=$row['method']?></td>
              <td><?=$row['stock']?></td>
              <td><button type="button" onclick="location.href='./product_update.php?id=<?=$row['idx']?>'">수정</button></td>
            </tr>
          <?php } ?>
      <?php } ?>
    </table>
  </div>
  <div>
    <table id="table2" class="table2">
      <tr>
        <th>외경</th>
        <th>두께</th>
        <th>재고</th>
      </tr>
    </table>
  </div>
  
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  // 페이지가 로드되면 실행
  $(document).ready(function() {
    // ajax로 데이터를 가져옴
    $.ajax({
      url: './product_stock.php',
      type: 'post',
      dataType: 'json',
      success: function(data) {
        if(data == 0) {
          $('#table2').append('<tr><td colspan="3">데이터가 없습니다.</td></tr>');
        } else {
          // 데이터를 테이블에 추가
          for (var i in data) {
            $('#table2').append('<tr><td>' + data[i].diameter + '</td><td>' + data[i].thickness + '</td><td>' + data[i].total_stock + '</td></tr>');
          }
          console.log(data)
        }
      }
    });
  });

</script>
<style>
  .table1 {
    width: 100%;
    border-collapse: collapse;
  }
  .table1 th, .table1 td {
    border: 1px solid #000;
    padding: 10px;
  }
  .table1 th {
    background-color: #f2f2f2;
  }
  .table2 {
    width: 100%;
    border-collapse: collapse;
  }
  .table2 th, .table2 td {
    border: 1px solid #000;
    padding: 10px;
  }
  .table2 th {
    background-color: #f2f2f2;
  }

</style>
</html>
