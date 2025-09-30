<?php
namespace App\Models;

use CodeIgniter\Model;

class BoardModel extends Model
{
    protected $table            = 'boards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title', 'contents', 'tag', 'viewcount', 'users', 'boardmaster'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'inputdate';
    protected $updatedField  = 'modifydate';
    protected $deletedField  = 'deletedate';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['removeUpdatedAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    # Insert 할 때 updated_at 제거
    protected function removeUpdatedAt(array $data)
    {
        // insert 할 때 updated_at 제거
        if (isset($data['data'][$this->updatedField])) {
            unset($data['data'][$this->updatedField]);
        }
        return $data;
    }

    #게시판 리스트
    public function getBoards_list($boardmaster = 1, $limit = 10, $search = null)
    {
        $sql = $this->select('
                            boards.id, boards.title, boards.viewcount, boards.inputdate
                            , u.nickname
                            , COUNT(DISTINCT b.id) as cmcnt
                            , COUNT(c.id) AS upcnt
                            , COUNT(d.id) AS downcnt
                            ')
                    ->join('users u', 'boards.users = u.id')
                    ->join('boardCmts b', 'boards.id = b.board', 'left')
                    ->join('board_ucnts c', 'boards.id = c.board', 'left')
                    ->join('board_dcnts d', 'boards.id = d.board', 'left')
                    ->where("boards.boardmaster", $boardmaster);
                    if (!empty($search)) {
                    $sql->groupStart()
                            ->like('boards.title', $search)
                            ->orLike('boards.contents', $search)
                        ->groupEnd();
                    }
                    $sql->groupBy('boards.id, boards.title, boards.viewcount, boards.inputdate, u.nickname')
                        ->orderBy('boards.id', 'DESC');
        return $sql->paginate($limit);
    }

    //게시판 상세보기
    public function getBoards_view($id, $userid = null)
    {
        return $this->select('
                              boards.id, boards.title, boards.contents, boards.viewcount, boards.inputdate, boards.users, u.nickname, u.userid, bk.userid as bookid
                            , COUNT(DISTINCT b.id) as cmcnt
                            , COUNT(DISTINCT c.id) AS upcnt
                            , COUNT(DISTINCT d.id) AS downcnt
                            ')
                    ->join('users u', 'boards.users = u.id') // boards.users = 회원 id
                    ->join('boardCmts b', 'boards.id = b.board', 'left')
                    ->join('board_ucnts c', 'boards.id = c.board', 'left')
                    ->join('board_dcnts d', 'boards.id = d.board', 'left')  
                    ->join('board_bmks bk', 
                        $userid !== null
                            ? "boards.id = bk.board and bk.userid = " . $this->db->escape($userid)
                            : "boards.id = bk.board ", 
                            'left'
                    ) 
                    ->where('boards.id', $id)
                    ->groupBy('boards.id, boards.title, boards.contents, boards.viewcount, boards.users, boards.inputdate, u.nickname, u.userid, bk.userid')
                    ->first();
    }    
    
    #게시판 수정 데이터 불러오기
    public function getBoards_update($id)
    {
        return $this->select('id, title, tag, contents, users')
                    ->where('boards.id', $id)
                    ->first();
    }

    #게시판 글쓰기, 수정
    public function boards_insert(array $postdata, $boardmaster)
    {
        $session   = session();

        $data = [
            'title'         => $postdata['title'],
            'contents'      => $postdata['contents'], 
            'tag'           => $postdata['tag'] ?? null,
            'users'         => $session->get('uid'), 
            'boardmaster'   => $boardmaster,
        ];
        return $this->insert($data);
    }

    public function boards_update(array $postdata, $id)
    {
        return $this->set([ 'title' => $postdata['title'], 'contents' => $postdata['contents'], 'tag' => $postdata['tag'] ])
                    ->where('id', $id)
                    ->update();    
    }

}
