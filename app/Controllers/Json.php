<?php
namespace App\Controllers;


use App\Models\BoarducntsModel;
use App\Models\BoarddcntsModel;
use App\Models\BoardbmksModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Json extends BaseController
{
    public function index()
    {
        //
    }
    public function upload(): ResponseInterface
    {
        $file = $this->request->getFile('upimage');

        // 업로드 파일 존재 확인
        if (!$file || !$file->isValid()) {
            if($file === null) 
                return $this->response->setJSON(['url' => 'file=null']);
            else
                return $this->response->setJSON(['url' => 'err']);
        }
        // 파일 확장자 체크 (선택)
        /*$allowedTypes = ['jpg', 'png', 'gif'];
        if (!in_array($file->getExtension(), $allowedTypes)) {
            return redirect()->back()->with('error', '허용되지 않은 파일 형식입니다');
        }*/

        // 업로드 폴더 지정 (writable)
        // WRITEPATH : CodeIgniter4에서 WRITEPATH는 쓰기 가능한 디렉토리의 절대 경로를 의미하는 **상수(Constant)**
        // 기본적으로 CI4 설치 시 writable/ 폴더를 가리킵
        // CI4에서는 로그, 캐시, 업로드 파일 등 서버에서 쓰기 필요한 모든 데이터를 저장하는 기본 폴더를 writable/로 관리
        // D:\php\ci4\writable\  - writable 는 웹에서 직접 전급할 수 없음
        //$uploadPath = WRITEPATH . 'uploads/images'; 
        $uploadPath = FCPATH . 'upload/images/';  // FCPATH : public 폴더 절대 경로

        // 파일 이름 중복 방지하여 이동
        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);    

        //return $this->response->setJSON(['url' => base_url('upload/images/' . $newName)]); //도메인 포함
        return $this->response->setJSON(['url' => '/upload/images/' . $newName]); //도메인 미포함
    }

    public function delete(): ResponseInterface
    {    
        $json = $this->request->getJSON(true);  // true → 배열로 받기
        $url  = $json['url'] ?? null;
        if (!$url) {
            return $this->response->setJSON([
                'errorflag' => 'e',
                'message'   => '삭제할 파일 URL이 없습니다.'
            ]);
        }

        // URL → 실제 서버 경로로 변환
        // 예: "/upload/images/20250913_abc.png"
        $filePath = FCPATH . ltrim(parse_url($url, PHP_URL_PATH), '/');
        if (!is_file($filePath)) {
            return $this->response->setJSON([
                'errorflag' => 'e',
                'message'   => '파일이 존재하지 않습니다.'
            ]);
        }

        // 파일 삭제
        if (@unlink($filePath)) {
            return $this->response->setJSON(['errorflag' => 's']);
        } else {
            return $this->response->setJSON([
                'errorflag' => 'e',
                'message'   => '파일 삭제 실패'
            ]);
        }
    }

    function recommend()
    {
        $session = session();
        $id = $this->request->getGet('id');
        $mode = $this->request->getGet('mode'); 

        if (!$session->get('userid')) { //로그인 정보 없음
            return $this->response->setJSON(['errorflag' => 'u']);  
        }else{
            $boardUcntsModel = new BoarducntsModel();
            $boardDcntsModel = new BoarddcntsModel();

            $board = $id;
            $users = $session->get('uid');
            $userid = $session->get('userid');

            $ucnt = $boardUcntsModel->getBoardUcnts($board, $users);
            $Dcnt = $boardDcntsModel->getBoardDcnts($board, $users);

            if($ucnt > 0 || $Dcnt > 0) { //이미 추천/비추천 한 경우
                return $this->response->setJSON(['errorflag' => 'x']);  
                exit;
            }

            if($mode == "u") {          //추천
                $boardUcntsModel->insert(['board' => $board, 'users' => $users, 'userid' => $userid]);
                $cnt = $boardUcntsModel->getBoardUcntsAll($board);

            }else if($mode == "d") {    //비추천
                $boardDcntsModel->insert(['board' => $board, 'users' => $users, 'userid' => $userid]);
                $cnt = $boardDcntsModel->getBoardDcntsAll($board);                
            }          
            return $this->response->setJSON(['errorflag' => 's', 'mode' => $mode, 'cnt' => $cnt]);
        }  
    }

    function bookmark()
    {
        $session = session();
        $id = $this->request->getGet('id');

        if (!$session->get('userid')) { #로그인 정보 없음
            return $this->response->setJSON(['errorflag' => 'u']);  
        }else{
            $boardBmksModel = new BoardbmksModel();
            $data = $boardBmksModel->getBoardsBmks($id, $session->get('uid'));

            if($data) { #이미 북마크 된 경우 → 삭제
                $boardBmksModel->where('id', $data['id'])->delete();
                $mode = "del"; #북마크 해제
            }else{      #북마크 안된 경우 → 추가
                $boardBmksModel->insert(['board' => $id, 'users' => $session->get('uid'), 'userid' => $session->get('userid')]);
                $mode = "add"; //북마크 설정
            }
            return $this->response->setJSON(['errorflag' => 's', 'mode' => $mode]);
        }  
    }
}
