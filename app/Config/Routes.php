<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
# ('url', '컨트롤러 클래스::메소드', [필터])
# get/post 구분 확인하기

# $routes->get('/', 'Home::index');
$routes->get('/', 'Board::index', ['as' => 'home']);    #홈페이지는 게시판 리스트로

#로그인/아웃
$routes->get('/user', 'User::index', ['as' => 'user.index']);                                   #로그인 폼
$routes->post('/user/login', 'User::login', ['as' => 'user.login', 'filter' => 'csrf']);        #로그인 처리, csrf필터는 post방식 처리페이지에 적용
$routes->post('/user/logout', 'User::logout', ['as' => 'user.logout']);                         #로그아웃 처리

#회원 가입/내정보
$routes->get('/user/create', 'User::create', ['as' => 'user.create']);                                   #가입 폼 
$routes->post('/user/store', 'User::store', ['as' => 'user.store', 'filter' => 'csrf']);          #가입 처리
$routes->get('/user/edit', 'User::edit', ['as' => 'user.edit', 'filter' => 'referercheck']);     #내정보 폼
$routes->put('/user/update', 'User::update', ['as' => 'user.update', 'filter' => 'csrf']);          #내정보 수정 처리

#게시판
$routes->get('/board', 'Board::index', ['as' => 'board.index']);                                        #리스트 
$routes->get('/board/(:num)', 'Board::show/$1', ['as' => 'board.show']);                                #상세보기      
$routes->get('/board/create', 'Board::create', ['as' => 'board.create']);                               #글쓰기 
$routes->post('/board', 'Board::store', ['as' => 'board.store', 'filter' => 'csrf']);                   #글쓰기처리    
$routes->get('/board/(:num)/edit', 'Board::edit/$1', ['as' => 'board.edit']);                           #수정폼   
$routes->put('/board/(:num)', 'Board::update/$1', ['as' => 'board.update', 'filter' => 'csrf']);        #수정처리   
$routes->delete('/board/(:num)', 'Board::destroy/$1', ['as' => 'board.destroy', 'filter' => 'csrf']);   #삭제처리

#json파일 처리
$routes->post('/json/upload', 'Json::upload');                      #게시판 에디터 이미지 첨부 json, POST방식으로
$routes->post('/json/delete', 'Json::delete');                      #게시판 에디터 이미지 삭제 json, POST방식으로
$routes->get('/json/recommend', 'Json::recommend');                 #게시판 추천/비추천 json
$routes->get('/json/bookmark', 'Json::bookmark');                   #게시판 추천/비추천 json

#댓글 처리
$routes->post('/boardcmts/write','Boardcmts::writePro', ['filter' => 'csrf']);            #댓글 쓰기