<?php
namespace App\Models;

use CodeIgniter\Model;

class BoardCmtModel extends Model
{
    protected $table            = 'boardcmts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['comment', 'gid', 'orderno', 'depth', 'users', 'board'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'comminputdate';
    protected $updatedField  = 'commmodifydate';
    protected $deletedField  = 'commdeletedate';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['removeUpdatedAt_comm'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    # Insert 할 때 updated_at 제거
    protected function removeUpdatedAt_comm(array $data)
    {
        // insert 할 때 updated_at 제거
        if (isset($data['data'][$this->updatedField])) {
            unset($data['data'][$this->updatedField]);
        }
        return $data;
    }

    public function getBoardcmts($id)
    {
        return $this->select('boardcmts.*, users.nickname, users.userid')
                    ->join('users', 'boardcmts.users = users.id')
                    ->where('boardcmts.board', $id)
                    ->orderBy('boardcmts.gid', 'ASC')
                    ->orderBy('boardcmts.orderno', 'ASC')
                    ->findAll(); //페이징 없을 때
    }

    public function getMaxgid($board)
    {
        $row = $this->select('IFNULL(MAX(gid), 0) + 1')
                    ->where('board', $board)
                    ->first();
        return array_values($row)[0]; 
    } 

    public function getMaxorderno($board, $gid)
    {
        $row = $this->select('max(orderno) + 1')
                    ->where('board', $board)
                    ->where('gid', $gid)
                    ->first();
        return array_values($row)[0]; 
    } 
}
