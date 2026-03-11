<?php

namespace App\Controllers;

use App\Models\BoardcmtModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Boardcmt extends BaseController
{
    protected $url;

    public function index()
    {
        //
    }

    public function store()
    {
        $session   = session();
        $boardcmtModel = new BoardcmtModel();

        $id = $this->request->getPost('id');            #댓글 id
        $board = $this->request->getPost("board");      #원 게시물 id
        $gid = $this->request->getPost("gid");
        $orderno = $this->request->getPost("orderno");
        $depth = $this->request->getPost("depth");
        $comment = $this->request->getPost("comment");
        $del = $this->request->getPost("del");

        $this->url = "boardmaster=" . $this->request->getGet("boardmaster");
        $this->url .= "&page=" . $this->request->getGet("page");
        $this->url .= $this->request->getGet("search") ? "&search=" . $this->request->getGet("search") : "";


        $db = \Config\Database::connect(); #트랜잭션 용도

        $db->transBegin();  #트랜잭션 시작
        try {
            #============================================
            # 작성 insert :id 없을 때
            #============================================
            if(!$id) { 

                if(!$gid) {     
                    /*
                    gid값이 없다 - 부모글이 없다, 1차 댓글
                        gid  = 그룹 아이디 : 원 게시물의 댓글 중 그룹아이디 max값 + 1
                        orderno = 정렬순서 : 1
                        depth = 들여쓰기깊이 : 1
                    */
                    $gid = $boardcmtModel->getMaxgid($board);   #최대 gid + 1
                    $orderno = 1;  
                    $depth = 1;
                }else{          
                    /*
                    gid값이 있다 - 부모글이 있다, 2차 댓글
                        gid  = 그룹 아이디 : 부모댓글과 동일 
                        orderno = 정렬순서 : 부모댓글 오더 + 1 / 같은 그룹에서 +1 된 자기보다 같거나 큰 orderno를 밀어낸다
                                    예)	부모 orderno 4
                                        입력될 orderno 4 +1 = 5
                                        기존의 5 같거나 큰 orderno + 1 = 6, 7, 8, 9....
                        depth = 들여쓰기깊이 : 부모댓글 뎁스 + 1
                    */
                    $depth = $depth + 1;
                    if($orderno == 1) {
                        $orderno = $boardcmtModel->getMaxorderno($board, $gid);
                    }else{
                        $orderno = $orderno + 1;
                        $boardcmtModel->set('orderno', 'orderno + 1', false)    #세 번째 인자를 false로 하면, $value가 escape 처리(''를 붙이지) 않고 SQL 그대로 실행
                                    ->where('board', $board)
                                    ->where('gid', $gid)
                                    ->where('orderno >=', $orderno)
                                    ->update();                    
                    }
                }
                $boardcmtModel->insert([
                    'comment'   => $comment,
                    'gid'       => $gid,
                    'orderno'   => $orderno,
                    'depth'     => $depth,
                    'users'     => session()->get('uid'),
                    'board'     => $board
                ]);
            #============================================
            # 수정   update
            #============================================                
            }else{
                if(!$del) {  #삭제가 아니다 - 수정
                    $boardcmtModel->set(['comment' => $comment])
                                ->where('id', $id)
                                ->update();   
                }else{      #삭제
                    $boardcmtModel->where('id', $id)->delete();
                }
            }
            $db->transCommit(); #커밋
            return redirect()->to(route_to("board.show",  $board) . "?" . http_build_query($this->request->getGet()));

        } catch (\Exception $e) {
            $db->transRollback(); #롤백     
            $session->setFlashdata('error', '저장중 오류발생: ' . $e->getMessage());            
            return redirect()->back()->withInput();
        }
    }
}
