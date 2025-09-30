<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['userid', 'password', 'name', 'nickname', 'email', 'mobile', 'profileimg', 'mailreceive', 'mobileauth', 'isactive', 'isstaff', 'joinedate', 'outdate']; 
								//'useterms', 'infopolicy': 가입폼에서 체크하지 않으면 가입처리 불가, DB 티폴드 값 1

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'joinedate';
    //protected $updatedField  = 'updated_at';
    protected $deletedField  = 'outdate';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function getUser()    
    {
        $session = session();

        return $this->where('userid', $session->get('userid'))
                    ->first();
    }

}
