<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/t_pass/adm/_common.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <!-- 제품등록페이지 -->
  <div>
    <h1>제품등록</h1>
    <form action="./product_insert.php" method="post" class="form">
      <div>
        <label for="diameter">외경</label>
        <input type="text" id="diameter" name="diameter">
      </div>
      <div>
        <label for="thickness">두께</label>
        <input type="text" id="thickness" name="thickness">
      </div>
      <div>
        <label for="material">소재</label>
        <input type="text" id="material" name="material">
      </div>
      <div>
        <label for="method">생산방식</label>
        <input type="text" id="method" name="stock">
      </div>
      <div>
        <label for="stock">재고</label>
        <input type="text" id="stock" name="stock">
      </div>
      <button type="button" id="btn_insert">등록</button>
    </form>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  // 등록 버튼 클릭시
  $('#btn_insert').on('click', function() {
    // 입력값이 있는지 확인
    if ($('#diameter').val() == '') {
      alert('외경을 입력해주세요');
      $('#diameter').focus();
      return false;
    }
    if ($('#thickness').val() == '') {
      alert('두께를 입력해주세요');
      $('#thickness').focus();
      return false;
    }
    if ($('#material').val() == '') {
      alert('소재를 입력해주세요');
      $('#material').focus();
      return false;
    }
    if ($('#stock').val() == '') {
      alert('재고를 입력해주세요');
      $('#stock').focus();
      return false;
    }
    alert('제품이 등록되었습니다.');
    // form 전송
    $('form').submit();
  });

</script>
<style>
  .form {
    width: 300px;
    margin: 0 auto;
  }
  .form div {
    margin-bottom: 10px;
  }
  .form label {
    display: block;
  }
  .form input {
    width: 100%;
  }
  .form button {
    width: 100%;
  }
</style>
</html>