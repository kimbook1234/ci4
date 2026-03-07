<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>로그인</title>
	<link rel="stylesheet" as="style" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.5/dist/web/static/pretendard-dynamic-subset.css"/>
  <style>
	* {
		font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 'Segoe UI',
        Roboto, 'Helvetica Neue', Arial, 'Noto Sans KR', sans-serif;
		box-sizing: border-box;
	}
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background: #f5f6fa;
    }

    .login-container {
      width: 100%;
      max-width: 360px;
      background: #fff;
      padding: 32px 24px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .login-container h1 {
      font-size: 1.4rem;
      margin-bottom: 20px;
      text-align: center;
    }

    .login-container input {
      width: 100%;
      padding: 12px 14px;
      margin-bottom: 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .login-container button {
      width: 100%;
      padding: 12px 14px;
      background: #4a6cf7;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      font-weight: bold;
      color: #fff;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .login-container button:hover {
      background: #3a57c4;
    }

    .login-links {
      margin-top: 16px;
      display: flex;
      justify-content: space-between;
      font-size: 13px;
    }

    .login-links a {
      text-decoration: none;
      color: #4a6cf7;
    }

    .login-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h1>로그인</h1>
<?//php echo password_hash('1942kk2', PASSWORD_DEFAULT);?>
    <form method="post" action="<?= route_to("user.login") ?>?>" name="logform" Onsubmit="return loginf()">
    <?= csrf_field() ?>
      <input type="hidden" name="referpage" value="<?= previous_url()?>">
      <input type="text" placeholder="아이디" name="userid" id="userid_id" onkeyup="chkInputValue(this)">
      <input type="password" placeholder="비밀번호" name="password" id="password_id" onkeyup="chkInputValue(this)">
      <button type="submit">로그인</button>
    </form>
    <div class="login-links">
      <a href="#">아이디/비밀번호 찾기</a>
      <a href="/user/joinForm">회원가입</a>
    </div>
    <div id="msgbox" style="color:red; font-size:13px; height: 25px; margin-top: 5px;">&nbsp;
	<?php if (session()->getFlashdata('error')): ?>
    <p style="color:red"><?= session()->getFlashdata('error') ?></p>
	<?php endif; ?>
	</div>

  </div>
</body>
<script type="text/javascript">
<!--
const loginf = function() {
	var f = document.logform;
	if(!f.userid.value) { 
		document.querySelector("#msgbox").innerHTML = "아이디를 입력해주세요";
		f.userid.focus();
		return false;
	}
	if(!f.password.value) { 
		document.querySelector("#msgbox").innerHTML = "비밀번호를 입력해주세요";
		f.password.focus();
		return false;
	}
}
const chkInputValue = obj => { 
	if(obj.value) document.querySelector("#msgbox").innerHTML = "&nbsp;"; 
}
//-->
</script>
</html>