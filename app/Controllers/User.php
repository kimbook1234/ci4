<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class User extends BaseController
{
    public function index()
    {
        return view('/users/login');
    }
	public function login()
	{
        $session = session();
        $userModel = new UserModel();

        $userid = $this->request->getPost('userid');
        $password = $this->request->getPost('password');
        $referpage = $this->request->getPost('referpage');    //로그인폼의 이전 페이지    

        $rs = $userModel->where('userid', $userid)->first();

        if ($rs && password_verify($password, $rs['password'])) {
            // 로그인 성공
            $session->set([
                'uid'       => $rs['id'],
                'userid'    => $rs['userid'],                                
                'nickname'  => $rs['nickname'],
                'isstaff'   => $rs['isstaff'],
                'logged'    => true,
            ]);

            if($referpage && !str_contains($referpage, '/user') ){
                return redirect()->to($referpage);
            }else{
                return redirect()->to(route_to('board.index'));
            }
            
        } else {
            // 로그인 실패
            $session->setFlashdata('error', '아이디 또는 비밀번호가 올바르지 않습니다. 애러입니다');
            return redirect()->back();
        }
	}

    public function logout()
    {   
        $session = session();
        $nowpage = $this->request->getPost('nowpage');
        
        $session->destroy(); // 모든 세션 삭제
        if($nowpage) {
            return redirect()->to($nowpage);            
        }else{
            return redirect()->to(route_to('home'));
        }
    }

    public function create()
    {
        return view('/users/create');
    }

    public function edit()
    {
        $userModel = new UserModel();
        $data['rs'] = $userModel->getUser();

        return view('/users/edit', $data);
    }

    public function store()
	{
        $session   = session();
		$userModel = new UserModel();
        
        $userid         = $this->request->getPost('userid');
        $password       = $this->request->getPost('password');
        $uname          = $this->request->getPost('uname');
        $nickname       = $this->request->getPost('nickname');
        $email          = $this->request->getPost('email');
        $mailreceive    = $this->request->getPost('mailreceive');
        $useterms       = $this->request->getPost('useterms');
        $infopolicy     = $this->request->getPost('infopolicy');

        // 이미 존재하는 아이디 체크
        if ($userModel->where('userid', $userid)->first()) {
            $session->setFlashdata('error', '이미 사용중인 아이디입니다.');
            return redirect()->back()->withInput();
        }
        // 이용약관 미체크
        if (!$useterms) {
            $session->setFlashdata('error', '이용약관에 동의해주세요.');
            return redirect()->back()->withInput();
        }
        // 이용약관 미체크
        if (!$infopolicy) {
            $session->setFlashdata('error', '개인정보처리방침에 동의해주세요.');
            return redirect()->back()->withInput();
        }

		$hashpass = password_hash($password, PASSWORD_DEFAULT);	//패스워드 암호화

        $userModel->insert([
            'userid'            => $userid,
            'password'          => $hashpass,
            'name'              => $uname,
            'nickname'          => $nickname,
            'email'             => $email,
            'mailreceive'       => $mailreceive,
            'useterms'          => $useterms,
            'infopolicy'        => $infopolicy,
        ]);
        return redirect()->to(route_to('user.index'));
	}

    public function update()
	{
        #$session   = session();
		$userModel = new UserModel();

        $id             = $this->request->getPost('id');
        $userid         = $this->request->getPost('userid');        
        $password       = $this->request->getPost('password');
        if(!empty($password))
            $hashpass   = password_hash($password, PASSWORD_DEFAULT);	//패스워드 암호화

        $uname          = $this->request->getPost('uname');
        $nickname       = $this->request->getPost('nickname');
        $email          = $this->request->getPost('email');
        $mailreceive    = $this->request->getPost('mailreceive');        

        $data = [
            'name'       => $uname,
            'nickname'   => $nickname,
            'email'      => $email,
            'mailreceive'=> $mailreceive,
        ];

        if (!empty($hashpass)) 
            $data['password'] = $hashpass;
          
        $userModel->set($data) 
                    ->where('id', $id)
                    ->where('userid', $userid)                    
                    ->update();   

        return redirect()->to(route_to('user.edit'));
    }
}
