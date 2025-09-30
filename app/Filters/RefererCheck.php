<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RefererCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $referer = $request->getServer('HTTP_REFERER');
        if (empty($referer)) {
            return service('response')->setStatusCode(403)->setBody('잘못된 접근입니다.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 필요 시 후처리
    }
}