<?= $this->include('common/header') ?>
<style>
.toastui-editor-contents {
  min-height: 450px; /* 이코드가 지정되어야 내용영억 어디를 클릭해도 커서거 생성됨 */
}
.toastui-editor-contents h1,
.toastui-editor-contents h2 {
	margin: 20px 0 10px;
	font-weight: bold;
}
</style>
<form method="post" action="/board/writePro?<?= $url?>" id="wform">
<?= csrf_field() ?>
<input type="hidden" name="id" value="<?= $id ?? ''?>">
<input type="hidden" name="users" value="<?= $rs['users'] ?? ''?>">
<div style="max-width:900px; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom:20px;">게시글 작성</h2>
    <div style="margin-bottom:15px;">
        <label for="postTitle" style="display:block; margin-bottom:5px; font-weight:500;">제목</label>
        <input type="text"  name="title" id="title" value="<?= esc($rs['title'] ?? '')?>" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px; font-size:14px; box-sizing:border-box;">
    </div>
    <div style="margin-bottom:15px;">
        <label for="postTags" style="display:block; margin-bottom:5px; font-weight:500;">태그 (쉼표로 구분)</label>
        <input type="text" name="tag" id="tag" value="<?= esc($rs['tag'] ?? '')?>" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px; font-size:14px; box-sizing:border-box;">
    </div>
    <div style="margin-bottom:15px;">
        <label for="postContent" style="display:block; margin-bottom:5px; font-weight:500;">내용</label>
		<div id="editor" class="toastui-editor-contents" style="width:100%;height:500px;"></div>
		<textarea name="contents" id="contents" style="display:none;"></textarea>
    </div>
    <div style="text-align:right;">
        <button type="submit" class="primaryBtn">작성</button>
    </div>
	<div id="writemsgbox" style="color:red;font-size:13px; height:25px; margin-top:5px;"></div>
</div>
<form>
<?= $this->include('common/footer') ?>
<script type="text/javascript">
<!--
let uploadedImages = [];

const editor = new toastui.Editor({
	el: document.querySelector('#editor'),
	minHeight: '450px',			//초기 시작 높이
	height: 'auto',				//자동으로 늘어나게
	initialEditType: 'wysiwyg',
	previewStyle: 'vertical',
	usageStatistics: false,
	initialValue: "<?= $rs['contents'] ?? ''?>",
    hooks: {
		addImageBlobHook: (blob, callback) => {
			const formData = new FormData();
            formData.append('upimage', blob, 'image.png'); //세번째 인자로 임시 파일명 지정, 그냥 “라벨" 같은 역활
			//=====================================================================
			//비동기 처리 : 서버로 이미지 업로드 요청
            fetch('/json/upload', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
				if(data.url != 'err') {
                	const imageUrl = data.url; // 서버에서 반환받은 이미지 URL
					uploadedImages.push(imageUrl);
                	callback(imageUrl, ''); // 콜백 함수에 URL 전달
				}else{
					 console.error('err:', error);
				}
            })
            .catch(error => {
                console.error('Editor error:', error);
                // 에러 처리 로직 추가
            });
			//=====================================================================
        }
    }
});	


//폼 체크
$(function() { //문서가 준비 되면 실행
	//=====================================
	//폼 체크
	//=====================================
	$("#wform").on("submit", function(e) {
		if (!$.trim($("#title").val())) {
			$("#writemsgbox").text("제목을 입력해주세요");
			$("#title").focus();
			e.preventDefault();		
			return false;
		}
		$("#contents").val(editor.getHTML().trim());
		if (!$.trim($("#contents").val()) || $.trim($("#contents").val()) == "<p><br></p>") { 
			$("#writemsgbox").text("내용을 입력해주세요");
			editor.focus();
			e.preventDefault(); 
			return false;
		}
		if(!confirm("작성 하시겠습니까?")) { 
			e.preventDefault();
			return false;
		}
	});

	$("#title").on("keyup", function() {
		if ($(this).val()) 	$("#writemsgbox").text("");   // jQuery 방식
	});

	//=====================================
	//toast ui 에디터 이벤트
	//=====================================
	editor.on('change', () => {
		let content = editor.getHTML().trim();  
		if (content) $("#writemsgbox").text("");   

		//에디터에 입력내용에 따라 높이가 자동으로 늘어나고 스크롤 안생기게 처리
		const contentEl = editor.getEditorElements().wysiwyg;  
		if (contentEl) {
			const body = contentEl.querySelector('.toastui-editor-contents');
			if (body) {
				const newHeight = Math.max(500, body.scrollHeight + 20); 
				editor.getRootElement().style.height = newHeight + 'px';
			}
		}

		//에디터에서 이미지 삭제시 서버에서도 삭제
		const contents = editor.getHTML();
		// 현재 본문에 존재하는 이미지 추출
		const parser = new DOMParser();
		const doc = parser.parseFromString(contents, "text/html");
		const currentImages = Array.from(doc.querySelectorAll("img"))
								   .map(img => img.getAttribute("src"));

		// uploadedImages 중에서 현재 없는 이미지 = 삭제된 이미지
		const deletedImages = uploadedImages.filter(url => !currentImages.includes(url));

		// 삭제 API 호출
		deletedImages.forEach(url => {
			//=====================================================================			
			//비동기 처리
			fetch("/json/delete", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ url })
			})
			.then(response => response.json())
			.then(jason => {
				if (json.errorflag == "e") { //url 파싱 실패
					alert("이미지 삭제 애러 발생");
					console.log("애러 : " + json.message);
					return;
				}
			})
			.catch(error => console.log(error));
			//=====================================================================
		});
		uploadedImages = currentImages;
	});
});

//-->
</script>