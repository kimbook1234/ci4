<?php
namespace App\Controllers;

USE App\Models\BoardModel;
use App\Models\BoardcmtModel;
use App\Models\BoardimgModel;
use App\Models\BoardmasterModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Board extends BaseController
{
    protected $boardmaster;
    protected $search;
    protected $catetag;
    protected $url;
    protected $pattern;
    protected $boardname;

    public function __construct()
    {
        $boardmasterModel = new BoardmasterModel();
        $this->boardmaster = service('request')->getGet('boardmaster') ?? 1;
        $this->search = service('request')->getGet('search') ?? null;   
        $this->catetag = service('request')->getGet('catetag') ?? null;           
        $this->boardname = $boardmasterModel->getBoardmasters($this->boardmaster)['boardname'] ?? '게시판';
        
        #get변수 url에 갖고 다니기 (페이징부분은 자동처리 됨)
        #$this->url = "boardmaster={$this->boardmaster}";
        #$this->url .= $this->search ? "&search=" . urlencode($this->search) : "";

        $this->pattern = '/<img[^>]+src=["\']([^"\']+)["\']/i'; # <img src="..."> 추출 패턴
    }

    # 리스트
    public function index() 
    {
        $boardModel = new BoardModel();
        $data['rss'] = $boardModel->getBoards_list($this->boardmaster, 10, $this->search, $this->catetag); //페이지당 글 개수 10
        $data['pager'] = $boardModel->pager;
        $data['search'] = $this->search ? $this->search : '';        
        $data['boardMasterData'] = $this->boardMasterData; #게시판 마스터 데이터
        $data['boardname'] = $this->boardname;  #게시판 이름
        $data['boardtagData'] = $this->boardtagData; #게시판 태그 데이터
        $data['boardmaster'] = $this->boardmaster; #게시판 코드
        return view('/boards/list', $data);  #'parser' => true 옵션은 view에서 파서 문법 사용하기 위함
    }

    # 상세보기
    public function show($id)
    {
        $boardModel = new BoardModel();      
        $BoardcmtModel = new BoardcmtModel();  
        $session = session();
        
        #동일접속자 조회수 중복증가 방지 세션처리
        $sessionViewKey = 'board_view_' . $id;
        if (!$session->has($sessionViewKey)) {
            $session->set($sessionViewKey, true);
            #조회수 업데이트
            $boardModel->set('viewcount', 'viewcount + 1', false)
                    ->where('id', $id)
                    ->update();        
        }
       
        $rs = $boardModel->getBoards_view($id, $session->get('userid'));
        $crss = $BoardcmtModel->getBoardcmts($id);

        $page = $this->request->getGet('page');

        return view('/boards/show', [
            'rs'    => $rs,
            'crss'  => $crss,            
            'page'  => $page, 
            'boardMasterData' => $this->boardMasterData, #게시판 마스터 데이터
            'boardname' => $this->boardname, #게시판 이름
            'boardtagData' => $this->boardtagData, #게시판 태그 데이터
            'boardmaster' => $this->boardmaster,
        ]);
    }

    public function create()
    {
        return view('/boards/create', [ 
            'boardMasterData' => $this->boardMasterData, #게시판 마스터 데이터
            'boardname' => $this->boardname, #게시판 이름
            'boardtagData' => $this->boardtagData, #게시판 태그 데이터
            'boardmaster' => $this->boardmaster,            
        ]); 
    }

    public function edit($id)
    {
        $boardModel = new BoardModel();             
        $rs = $boardModel->getBoards_update($id);
        $data = [ 
            'id' => $id, 
            'rs' => $rs, 
            'boardMasterData' => $this->boardMasterData,
            'boardname' => $this->boardname, #게시판 이름      
            'boardtagData' => $this->boardtagData, #게시판 태그 데이터
            'boardmaster' => $this->boardmaster,            
        ];

        if (isset($data['rs']['contents']))
            $data['rs']['contents'] = str_replace('"', '\"', $data['rs']['contents']);    #치환 적용

        return view('/boards/edit', $data);
    }


    #게시판 작성 처리
    public function store()
    {
        $session   = session();
        $boardModel = new BoardModel();
        $boardImgModel = new BoardimgModel();
        $db = \Config\Database::connect(); #트랜잭션 용도

        #$title = $this->request->getPost('title');        
        #$tag = $this->request->getPost('tag');                
        $contents = trim($this->request->getPost('contents'));  

        #본문에서 <img src="..."> 추출
        preg_match_all($this->pattern, $contents, $matches);
        $editor_img = $matches[1] ?? [];

        #============================================
        # 작성 insert
        #============================================
        $data = $this->request->getPost();

        $db->transBegin();  #트랜잭션 시작
        try {
            $insertId = $boardModel->boards_insert($data, $this->boardmaster);
            if (!$insertId) {
                $session->setFlashdata('error', '저장중 오류발생');
                return redirect()->back()->withInput();
            }
            #추출 img DB 저장 (boardimages 테이블)
            if (!empty($editor_img)) {
                foreach ($editor_img as $url)
                    $boardImgModel->insert(['imageurl' => $url, 'board' => $insertId]);
            }

            $db->transCommit(); #커밋
            return redirect()->to(route_to('board.index') . "?" . http_build_query($_GET));

        } catch (\Exception $e) {
            $db->transRollback(); #롤백
            $session->setFlashdata('error', '저장중 오류발생: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
    function update($id)
    {
        #============================================
        # 수정   update
        #============================================           
        $session   = session();
        $boardModel = new BoardModel();
        $boardImgModel = new BoardimgModel();
        $db = \Config\Database::connect(); #트랜잭션 용도       

        $logusers = $session->get('uid');
        $users = $this->request->getPost('users');
        $contents = trim($this->request->getPost('contents'));  
           
        if($users != $logusers)  #원작성자와 수정자 일치여부
            return redirect()->back()->withInput();

        #본문에서 <img src="..."> 추출
        preg_match_all($this->pattern, $contents, $matches);
        $editor_img = $matches[1] ?? [];

        $data = $this->request->getPost();

        $db->transBegin();  #트랜잭션 시작
        try {          
            $boardModel->boards_update($data, $id);
            $data = $boardImgModel->getBoardimgs($id);
        
            #기존 첨부된 이미지가 있을 때
            $db_img = [];
            #============================================
            if (!empty($data)) { 
                # $db_img : db 이미지 목록
                # $editor_img : 에디터에서 추출한 이미지 목록
                foreach($data as $rs)
                    $db_img[] = $rs['imageurl'];

                #1. db : 있다 / editor : 없다 → db 삭제, 서버파일 삭제
                $delete_img = array_diff($db_img, $editor_img);
                if (!empty($delete_img)) {
                    foreach ($delete_img as $url) {
                        $filepath = FCPATH . substr($url, 1);
                        if (file_exists($filepath))
                            unlink($filepath);              #파일 삭제
                    }
                    #DB에서 이미지 레코드 삭제
                    $boardImgModel->where('board', $id) 
                                ->whereIn('imageurl', $delete_img)
                                ->delete();
                }

                #2. editor : 있다 / db : 없다 → db 추가
                $insert_img = array_diff($editor_img, $db_img);
                if (!empty($insert_img)) {
                    foreach ($insert_img as $url)
                        $boardImgModel->insert([ 'imageurl' => $url, 'board' => $id ]);
                }

            #기존 이미지가 없을 때
            #============================================
            }else{
                if (!empty($editor_img)) {
                    $boardImgModel = new BoardimgModel();

                    foreach ($editor_img as $url)
                        $boardImgModel->insert([ 'imageurl' => $url, 'board' => $id ]);
                }
            }
            $db->transCommit(); #커밋
            
            return redirect()->to(route_to('board.show', $id) . '?' . http_build_query($_GET));

        } catch (\Exception $e) {
            $db->transRollback(); #롤백
            $session->setFlashdata('error', '수정중 오류발생: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    function destroy($id)
    {
        $session = session();          
        $boardModel = new BoardModel();
        $BoardcmtModel = new BoardcmtModel();
        $boardImgModel = new BoardimgModel();
        $db = \Config\Database::connect(); #트랜잭션 용도

        $crss = $BoardcmtModel->getBoardcmts($id);

        if (empty($crss)) { //댓글이 없으면 삭제
            
            $db->transBegin();  #트랜잭션 시작
            try {    
                $rss = $boardImgModel->getBoardimgs($id);
                #서버에서 이미지 파일 삭제    
                if (!empty($rss)) {
                    foreach ($rss as $rs) {
                        $filepath = FCPATH . substr($rs['imageurl'], 1);
                        if (file_exists($filepath)) {
                            unlink($filepath); //파일 삭제
                        }
                    }
                    #DB 이미지 레코드 삭제
                    $boardImgModel->where('board', $id)->delete();
                }
                
                $boardModel->where('id', $id)->delete();
                $db->transCommit(); #커밋

                return redirect()->to(route_to('board.index') . '?' . http_build_query($_GET));
            } catch (\Exception $e) {
                $db->transRollback(); #롤백
                $session->setFlashdata('error', '삭제중 오류발생: ' . $e->getMessage());
                return redirect()->back();
            }
        }else{ //댓글 있으면 삭제 불가
            $session->setFlashdata('error', '댓글이 달린 게시물은 삭제 하실 수 없습니다.');
            return redirect()->back();
        }
    }
}
