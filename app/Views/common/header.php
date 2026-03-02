<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>BOOKPEN</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
	<link rel="stylesheet" as="style" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.5/dist/web/static/pretendard-dynamic-subset.css"/>
	<link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
    <link rel="stylesheet" href="<?= base_url('css/editor.css') ?>">   
	<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
    <script src="<?= base_url('js/jquery.js') ?>"></script>
    <script src="<?= base_url('js/jquery-migrate-1.4.1.min.js') ?>"></script>    
	<style>
	/* 제목 링크 기본 스타일 제거 */
	table td a {
	  color: inherit;          /* 부모 글자색(검정) 상속 */
	  text-decoration: none;   /* 밑줄 제거 */
	}

	/* 마우스 오버 시 버튼 색상과 동일하게 */
	table td a:hover {
	  color: #4a6cf7; /* 작성하기 버튼 색상과 동일하게 맞춤 */
	}

	/* view.html 북마크용*/
	#bookmark { 
		transition: all 0.4s ease;
	}
	.toastui-editor-contents img {
		max-width: 100%;
		height: auto;
	}
	</style>
</head>
<body>
	<header>
	    <div class="logo">로컬 CI4</div>
	    <nav id="mainMenu" >
		   <a href="/board/list?boardmaster=1"><button data-menu="dashboard" class="active">게사판</button></a>
		   <!--<button data-menu="content">콘텐츠</button>
		   <button data-menu="users">사용자</button>
		   <button data-menu="settings">설정</button>-->
	    </nav>
	    
	    <!-- 로그인 / 회원가입 버튼 영역 -->
	    <div class="authButtons">
            <?php $session = session(); ?>
            <?php if ($session->get('logged')): ?>
            <!--  로그인 상태-->
                <button style="background: transparent;color: white; font-size: 16px;"><?= $session->get('nickname')?></button>
                <a href="javascript:logout()"><button class="loginBtn">로그아웃</button></a><a href="/user/editForm"><button class="signupBtn">내 정보</button></a>
            <?php else:?>
            <!--  로그아웃 상태-->
                <a href="/user"><button class="loginBtn">로그인</button></a>
                <a href="/user/joinForm"><button class="signupBtn">회원가입</button></a>
            <?php endif; ?>
	    </div>
	</header>
    <form method="post" action="/user/logout" name="logoutform">
    <input type="hidden" name="nowpage" value="<?= current_url();?>">
    </form>    

	<div class="layout">
		<aside id="leftQuick">
			<!--<p>왼쪽 퀵메뉴 / 광고 영역</p>
			<ul style="margin-top:10px; list-style:none; font-size:14px;">
				<li><a href="#">메뉴1</a></li>
				<li><a href="#">메뉴2</a></li>
				<li><a href="#">메뉴3</a></li>
			</ul>-->
		</aside>
		<main>
			<p>

