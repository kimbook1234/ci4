<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>회원가입</title>
	<link rel="stylesheet" href="/include/css/style.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" />
	<style>
	* { box-sizing: border-box; }

	body {
		font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, 'Segoe UI',
		Roboto, 'Helvetica Neue', Arial, 'Noto Sans KR', sans-serif;
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;
		margin: 0;
		background: #f5f6fa;
	}

	.signup-container {
		width: 100%;
		max-width: 400px;
		background: #fff;
		padding: 32px 24px;
		border-radius: 12px;
		box-shadow: 0 4px 20px rgba(0,0,0,0.1);
	}

	.signup-container h1 {
		font-size: 1.4rem;
		margin-bottom: 20px;
		text-align: center;
	}

	.signup-container input[type="text"],
	.signup-container input[type="password"],
	.signup-container input[type="email"] {
		width: 100%;
		padding: 12px 14px;
		margin-bottom: 14px;
		border: 1px solid #ccc;
		border-radius: 6px;
		font-size: 14px;
	}

	.signup-container label {
		display: flex;
		align-items: center;
		font-size: 13px;
		margin-bottom: 10px;
		cursor: pointer;
	}

	.signup-container label input {
		margin-right: 8px;
	}

	.signup-container button {
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
		margin-top: 10px;
	}

	.signup-container button:hover {
		background: #3a57c4;
	}

	.login-link {
		margin-top: 14px;
		text-align: center;
		font-size: 12px;
	}

	.login-link a {
		text-decoration: none;
		color: #4a6cf7;
	}

	.login-link a:hover {
		text-decoration: underline;
	}
	</style>
</head>
<body>
	<div class="signup-container">
		<h1>회원가입</h1>
		<form method="post" action="<?= route_to("user.store") ?>" name="joinform" Onsubmit="return joinf()">
		<?= csrf_field() ?>			
		<input type="text" placeholder="아이디" name="userid" id="userid_id" onkeyup="chkInputValue(this)" pattern="[A-Za-z0-9]*" >
		<input type="password" placeholder="비밀번호" name="password" id="password_id" onkeyup="chkInputValue(this)">
		<!-- <input type="password" placeholder="비밀번호 확인" required> -->
		<input type="email" placeholder="이메일" name="email" id="email_id" onkeyup="chkInputValue(this)">
		<input type="text" placeholder="실명" name="uname" id="uname_id" onkeyup="chkInputValue(this)" >
		<input type="text" placeholder="닉네임" name="nickname" id="nickname_id" onkeyup="chkInputValue(this)">

		<label><input type="checkbox" name="mailreceive" id="mailreceive_id" value="1"> 메일 수신 동의</label>
		<label><input type="checkbox" name="useterms" id="useterms_id"  value="1" > 이용약관 동의</label>
		<label><input type="checkbox" name="infopolicy" id="infopolicy_id"  value="1"> 개인정보처리방침 동의</label>
		
		<button type="submit">회원가입</button>
		</form>
		<div class="login-link">
		이미 계정이 있으신가요? <a href="<?= route_to("user.index") ?>">로그인</a>
		</div>
		<div id="msgbox" style="color:red; font-size:13px; height:25px; margin-top:5px;">&nbsp;</div>
		<div id="msgbox" style="color:red; font-size:13px; height:25px; margin-top:5px;">&nbsp;
		<?php if (session()->getFlashdata('error')): ?>
		<p style="color:red"><?= session()->getFlashdata('error') ?></p>
		<?php endif; ?>
		</div>

	</div>
</body>
</html>
<script type="text/javascript">
<!--
const joinf = function() {
	var f = document.joinform;
	if(!f.userid.value) { 
		document.querySelector("#msgbox").innerHTML = "아이디를 입력해주세요";
		f.userid.focus();
		return false;
	}
	if(!f.password.value) { 
		document.querySelector("#msgbox").innerHTML = "비밀번호를 입혁해주세요";
		f.password.focus();
		return false;
	}
	if(!f.email.value) { 
		document.querySelector("#msgbox").innerHTML = "이메일을 입력해주세요";
		f.email.focus();
		return false;
	}
	if(!f.uname.value) { 
		document.querySelector("#msgbox").innerHTML = "성명을 입력해주세요";
		f.uname.focus();
		return false;
	}
	if(!f.nickname.value) { 
		document.querySelector("#msgbox").innerHTML = "닉네임을 입력해주세요";
		f.nickname.focus();
		return false;
	}
	if(!f.useterms.checked) { 
		document.querySelector("#msgbox").innerHTML = "이용약관에 동의해주세요";
		f.useterms.focus();
		return false;
	}
	if(!f.infopolicy.checked) { 
		document.querySelector("#msgbox").innerHTML = "개인정보 처리방침에 동의해주세요";
		f.infopolicy.focus();
		return false;
	}
	if(!confirm("가입을 완료하시겠습니까?")) return false;
}

const chkInputValue = obj => { 
	if(obj.value) document.querySelector("#msgbox").innerHTML = "&nbsp;"; 
}
//-->
</script>