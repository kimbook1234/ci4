<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CodeIgniter4 재작</title>
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
	    <div class="logo"><a href="<?= route_to('board.index') ?>" style="color: #887979; text-decoration: none">KIM J-W <br><font style="font-size:17px">(CodeIgniter4)</font></a></div>
	    <nav id="mainMenu" >
			<?php if(!empty($boardMasterData) && is_array($boardMasterData)):?>
				<?php foreach($boardMasterData as $bm): ?>
					<a href="<?=  route_to('board.index') . '?boardmaster=' . $bm['id'] ?>"><button data-menu="dashboard" class="<?= ($boardmaster == $bm['id']) ? 'active' : '' ?>"><?= esc($bm['boardname']) ?></button></a>
				<?php endforeach ?>
			<?php endif ?>
	    </nav>
	    
	    <!-- 로그인 / 회원가입 버튼 영역 -->
	    <div class="authButtons">
            <?php $session = session(); ?>
            <?php if ($session->get('logged')): ?>
            <!--  로그인 상태-->
                <button style="background: transparent;color: #887979; font-size: 16px;"><?= $session->get('nickname')?></button>
                <a href="javascript:logout()"><button class="loginBtn">로그아웃</button></a><a href="<?= route_to('user.edit') ?>"><button class="signupBtn">내 정보</button></a>
            <?php else:?>
            <!--  로그아웃 상태-->
                <a href="<?= route_to('user.index') ?>"><button class="loginBtn">로그인</button></a>
                <a href="<?= route_to('user.create') ?>"><button class="signupBtn">회원가입</button></a>
            <?php endif; ?>
	    </div>
	</header>
    <form method="post" action="<?= route_to('user.logout') ?>" name="logoutform">
    	<input type="hidden" name="nowpage" value="<?= current_url();?>">
    </form>    

	<div class="layout">
		<aside id="leftQuick">
			<p style="font-size:16px;font-weight:bold;background-color:#4a5568; color:white; padding: 10px; border-radius: 4px;"><?= $boardname?></p>
			<ul style="margin-top:10px; list-style:none; font-size:15px; padding-left:0; ">		
			<?php if(!empty($boardtagData) && is_array($boardtagData)):?>
				<?php foreach($boardtagData as $bt): ?>
					<li style='padding: 5px 0'>
						<a href='<?= route_to('board.index') . "?boardmaster=" . service('request')->getGet('boardmaster') . "&catetag=". urlencode($bt['tag'])?>' style='text-decoration:none; color:black;'>
							<?= esc($bt['tag']) ?>
						</a>
					</li>
				<?php endforeach ?>
				<!--font-weight:bolder; color: red-->
			<?php endif ?>

		</aside>
		<main>
			<p>

