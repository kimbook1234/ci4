<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
# ('url', '컨트롤러 클래스::메소드', [필터])
# get/post 구분 확인하기

# $routes->get('/', 'Home::index');
$routes->get('/', 'Board::list');

#로그인/아웃
$routes->get('/user', 'User::index');				                                            #로그인 폼
$routes->post('/user/login', 'User::login', ['filter' => 'csrf']);                              #로그인 처리, csrf필터는 post방식 처리페이지에 적용
$routes->post('/user/logout', 'User::logout');		                                            #로그아웃 처리

#회원 가입/내정보
$routes->get('/user/joinForm', 'User::joinForm');	                                            #가입 폼 
$routes->post('/user/joinPro', 'User::joinPro', ['filter' => 'csrf']);	                        #가입 처리
$routes->get('/user/editForm', 'User::editForm', ['filter' => 'referercheck']);	                #내정보 폼
$routes->post('/user/editPro', 'User::editPro', ['filter' => 'csrf']);                          #내정보 수정 처리

#게시판
$routes->get('/board/list', 'Board::list');	                                                        #게시판 리스트 
$routes->get('/board/view/(:num)', 'Board::view/$1');	                                            #게시판 상세보기 
$routes->get('/board/writeForm(/(:num))?', 'Board::writeForm/$2', ['filter' => 'referercheck']);	#게시판 글쓰기 폼, url 뒤에 선택적 '/', 선택적 값 '?' 정규식처리
$routes->post('/board/writePro', 'Board::writePro', ['filter' => 'csrf']);	                        #게시판 글쓰기/수정 처리 
$routes->post('/board/delete', 'Board::delete', ['filter' => 'csrf']);                              #게시판 삭제 처리 

#json파일 처리
$routes->post('/json/upload', 'Json::upload');                      //게시판 에디터 이미지 첨부 json, POST방식으로
$routes->post('/json/delete', 'Json::delete');                      //게시판 에디터 이미지 삭제 json, POST방식으로
$routes->get('/json/recommend', 'Json::recommend');                 //게시판 추천/비추천 json
$routes->get('/json/bookmark', 'Json::bookmark');                   //게시판 추천/비추천 json

#댓글 처리
$routes->post('/boardcmts/write','Boardcmts::writePro', ['filter' => 'csrf']);            //댓글 쓰기