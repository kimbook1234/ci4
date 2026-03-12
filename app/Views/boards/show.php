<?= $this->include('common/header') ?>

<?php 
$session = session(); 
if (!$session->get('logged')) 
    $disabled = "disabled";
else
    $disabled = "";	
?>
<!-- 내용영역 -->
<!-- 게시글 보기 페이지 컨테이너 -->
<div style="max-width:900px; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
	<h2 style="margin-bottom:20px;"><?= $boardname?></h2>
	<!-- 작성 정보 -->
	<div style="font-size:14px; color:#555; margin-bottom:20px;display:flex; align-items:center;gap:5px;">
		<img src="/icon/1234.jpg" alt="프로필" style="width:30px; height:30px; border-radius:50%; object-fit:cover;"> 
		<span style="font-weight:bold; color:#000;"><?= esc($rs['nickname']) ?></span> | 조회수: <?= $rs['viewcount'] ?> | <?= $rs['inputdate'] ?>
	</div>
	<!-- 제목 -->
	<h1 style="font-size:30px; margin-bottom:20px;"><?= esc($rs['title']) ?></h1>
	<!-- 내용 영역 -->
	<div style="font-size:14px; line-height:1.6; min-height:300px; margin-bottom:30px;" class="toastui-editor-contents">
		<?= $rs['contents'] ?>
	</div>
	<!-- 추천 / 비추천 -->
	<div style="text-align:center; margin-bottom:20px;">
		<img src="/icon/like.png" width="25" style="cursor:pointer; vertical-align:middle;" id="upcntbtn" data-val1="<?= $rs['id'] ?>" data-val2="u">&nbsp;<span id="upcnt"><?= $rs['upcnt'] ?></span>&nbsp;&nbsp;
		<img src="/icon/dislike.png" width="25" style="cursor:pointer; vertical-align:middle;" id="downcntbtn" data-val1="<?= $rs['id'] ?>" data-val2="d">&nbsp;<span id="downcnt"><?= $rs['downcnt'] ?></span>
		<p>
		<?php if ($session->get('logged') && $rs["bookid"] !== null): ?>
		<!-- 북마크 내역이 있다면 -->
		<img src='/icon/heart_red.png' width='25' style='cursor:pointer;' id='bookmark' data-val='<?= $rs['id'] ?>'>
		<?php else:?>
		<!-- 북마크 내역이 없다면 -->
		<img src='/icon/heart.png' width='25' style='cursor:pointer;' id='bookmark' data-val='<?= $rs['id'] ?>'>
		<?php endif; ?>
	</div>
	
	<form method="POST" action="<?= route_to('board.delete', $rs['id']) . "?" . http_build_query($_GET) ?>" name="dform" id="dform">
	<?= csrf_field() ?>
	<input type="hidden" name="_method" value="DELETE">
	<input type="hidden" name="users" value="<?= $rs['users'] ?>">
	<div style="text-align:right;">
        <?php if ($session->get('userid') == $rs['userid']): ?>
            <a href="<?= route_to('board.edit', $rs['id']) . "?" . http_build_query($_GET) ?>"><button type="button" class="primaryBtn" style="margin-right:5px">수 정</button></a>
            <?php if ($rs['cmcnt'] == 0): ?>
            <button type="submit" class="primaryBtn">삭 제</button>
			<?php endif; ?>
        <?php endif; ?>
		<a href="<?= route_to("board.index") . "?" . http_build_query($_GET) ?>"><button type="button" class="primaryBtn">← 목록으로</button></a>
	</div>
	</form>
	<?php if (session()->getFlashdata('error')): ?>
    <p style="color:red"><?= session()->getFlashdata('error') ?></p>
	<?php endif; ?>
</div>

<!-- 댓글 영역 컨테이너 -->
<div style="max-width:900px; margin:20px auto; background:#f7fafc; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1); ">
	<h3 style="margin-bottom:10px;">댓글 <?= $rs['cmcnt']?> 개</h3>
	<!-- 댓글 입력폼 (맨 위) -->
	<form method="post" action="<?= route_to("boardcmt.store")?>" id="cmform" class="cmform">
	<?= csrf_field() ?>
	<input type="hidden" value="<?= $rs['id'] ?>" name="board">
	<div style="margin-bottom:20px;">
		<textarea placeholder="댓글을 입력하세요..." style="width:100%; height:150px; padding:10px; border:1px solid #ccc; border-radius:5px; font-size:13px; resize:none; box-sizing:border-box;" name="comment" id="comment" class="comment" <?=$disabled?>></textarea>
		<div style="text-align:right; margin-top:10px;"><button type="submit" class="primaryBtn" <?= $disabled?>>댓글 등록</button></div>
	</div>
	</form>
	<!-- 댓글 리스트 -->
	<div style="width:100%; box-sizing:border-box;">
		<!-- 첫 번째 댓글 -->
    <?php if(!empty($crss) && is_array($crss)):?>
        <?php foreach($crss as $crs): ?>
            <?php
                if($crs["depth"] > 1) {
					$margin = "80px";
					$remargin1 = "margin-left:40px";
					$remargin2 = "margin-right:0";
					$usermargin = "margin-left:40px"; //2차 댓글이면 "답글달기/수정/삭제" 오른쪽 마진
				}else{
					$margin = "40px";
					$remargin1 = "";
					$remargin2 = "";
					$usermargin = "";
                }
            ?>
		<form method="post" action="<?= route_to("boardcmt.store") . "?" . http_build_query($_GET)?>" id="cmform<?= $crs['id'] ?>" class="cmform">
		<?= csrf_field() ?>
		<input type="hidden" name="board" value="<?= $rs['id'] ?>" > <!-- 상세페이지 원게시글의 id -->
		<input type="hidden" name="gid" value="<?= $crs['gid'] ?>" >
		<input type="hidden" name="orderno" value="<?= $crs['orderno'] ?>">
		<input type="hidden" name="depth" value="<?= $crs['depth'] ?>">
		<input type="hidden" name="hidecomment" value="<?= $crs['comment'] ?>">
		<input type="hidden" name="hideid" value="<?= $crs['id'] ?>">
		<input type="hidden" name="id" value="<?= $crs['id'] ?>">
		<input type="hidden" name="del">
		<div style="background:#fff; padding:10px 15px; border-radius:5px; margin-bottom:10px; box-shadow:0 1px 2px rgba(0,0,0,0.05); font-size:13px; width:100%; box-sizing:border-box;">
			<div style="margin-bottom:8px; <?= $remargin1?>"><?= nl2br(esc($crs['comment'])) ?></div>
			<div style="color:#555; font-size:13px; margin-bottom:5px; <?= $remargin1?>"><span style="font-weight:bold; color:#000;"><?= esc($crs['nickname']) ?></span>
			<?php if($rs["users"] == $crs["users"]):?>
				<span style="background-color: #c8f0f7ff; color: #353838ff; font-size:12px; margin-left:5px; padding: 2px 4px; border-radius: 8px;">작성자</span>
			<?php endif; ?>
			| <?= esc($crs['comminputdate']) ?></div>
			<div style="text-align:right; font-size:13px; <?= $remargin2?>">
			</div>
			<div style="<?= $usermargin?>">
				<?php if ($session->get('logged')): ?>
					<span style="color:blue;margin-right:5px;cursor:pointer" class="showrepform">답글달기</span>
					<?php if ($session->get('userid') == $crs['userid']): ?>
						<span style="color:red;margin-right:5px;cursor:pointer" class="showmodform">수정</span>
						<span style="color:red;cursor:pointer" class="cmdelete">삭제</span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<!-- 첫 번째 댓글 답글 입력폼 -->
			<div style="margin-top:10px; margin-left:<?= $margin?>;display:none" id="cmform_div<?= esc($crs['id']) ?>" class="cmform_div">
				<textarea placeholder="답글을 입력하세요..." style="width:100%; height:80px; padding:8px; border:1px solid #ccc; border-radius:5px; font-size:13px; resize:none; box-sizing:border-box;" name="comment" class="comment" <?=$disabled?>></textarea>
				<div style="text-align:right; margin-top:5px;">
					<button type="submit" style="padding:5px 12px; background:#2d3748; color:white; border:none; border-radius:5px; cursor:pointer;" <?=$disabled?>>답글 등록</button>
				</div>
			</div>
		</div>
		</form>
        <?php endforeach ?>
    <?php endif ?>
	</div>
</div>

<?= $this->include('common/footer') ?>
<script type="text/javascript">
<!--
$(function() {
	//게시글 삭제
	$("#dform").on("submit", function(e) {
		if(!confirm("삭제 하시겠습니까?")) { 
			e.preventDefault();
			return false;
		}
	});

	//댓글 작성
	$(".cmform").on("submit", function(e) {
		let $cm = $(this).find(".comment");  
		if ($(this).find("[name='del']").val() == "1") return true;  //댓글삭제시 e.preventDefault() 때문에 서밋이 안됨 del 값이 1이면 e.preventDefault()이 실행 안되게 막는다. 여기서 중단

		if (!$cm.val().trim()) {
			$cm.focus();
			e.preventDefault();
			return false;
		}
		if(!confirm("작성 하시겠습니까?")) { 
			e.preventDefault();
			return false;
		}
	});

	//수정폼 열기
	$(".showmodform").on("click", function() {
		let $f = $(this).closest(".cmform");
		if ($f.find(".cmform_div").is(":visible")) {
			$f.find(".cmform_div").slideUp(); //숨기기
		} else {
			$f.find(".cmform_div").slideDown(); //열기
			$f.find("[name='id']").val($f.find("[name='hideid']").val());
			$f.find(".comment").val($f.find("[name='hidecomment']").val());
		}
	});

	//2차 댓글입력폼 열기
	$(".showrepform").on("click", function() {
		let $f = $(this).closest(".cmform");
		if ($f.find(".cmform_div").is(":visible")) {
			$f.find(".cmform_div").slideUp(); //숨기기
		} else {
			$f.find(".cmform_div").slideDown(); //열기
			$f.find("[name='id']").val("");
			$f.find(".comment").val("");
		}
	});

	//댓글 삭제
	$(".cmdelete").on("click", function() {
		let $f = $(this).closest(".cmform");
		$f.find("[name='del']").val(1);		// cmform폼 안에 있는 name='del'인것	
		if(!confirm("삭제 하시겠습니까?")) return;
		$f.submit();
	});


	$("#bookmark").on("click", function(e) {
		let v = $(this).data("val"); //id
		//mode : 북마크 설정/취소 구분
		fetch(`/json/bookmark?id=${v}`)
		.then(response => response.json())
		.then(json => {
			if(json.errorflag == "u") { //로그인 정보 없음
				alert("로그인후 이용해주세요");
				return;
			}else if(json.errorflag == "e") { //애러 발생
				alert("애러 발생");
				return;
			}else {//성공
				if(json.mode == "add") { //북마크 됨
					$("#bookmark").attr("src", "/icon/heart_red.png");
				}else if(json.mode == "del") { //북마크 해제됨
					$("#bookmark").attr("src", "/icon/heart.png");
				}else{
					alert("예외 발생");
				}
			}
		})
		.catch(error => console.log(error));
	});

	$("#upcntbtn, #downcntbtn").on("click", function(e) {
		let v1 = $(this).data("val1"); //id
		let v2 = $(this).data("val2"); //구분

		fetch(`/json/recommend?id=${v1}&mode=${v2}`)
		.then(response => response.json())
		.then(json => {
			if(json.errorflag == "u") { //로그인 정보 없음
				alert("로그인후 이용해주세요");
				return;
			}else if(json.errorflag == "e") { //애러 발생
				alert("애러 발생");
				return;
			}else if(json.errorflag == "x") { //이미 추천함
				alert("이미 추천/비추천한 게시글입니다");
				return;
			}else {//성공
				if(v2 == "u") {
					$("#upcnt").text(json.cnt);
				}else if(v2 == "d") {
					$("#downcnt").text(json.cnt);
				}else{
					alert("예외 발생");
				}
			}
		})
		.catch(error => console.log(error));
	})
});
//-->
</script>