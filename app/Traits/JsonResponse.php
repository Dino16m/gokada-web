<?php
namespace App\Traits;

trait JsonResponse 
{
    public function jsonResponse($arr, $code=200)
    {
        
        return response()->json([
            'status'=>$arr['status'],
            'error'=>$arr['error'] ?? [] ,
            'data'=>$arr['data'] ?? [],
            'numberOfPages'=> $arr['numberOfPages'] ?? 1,
            'currentPage' => $arr['currentPage'] ?? 1,
            'hasNextPage'=>$arr['hasNextPage'] ?? false,
            'nextPage'=>$arr['nextPage'] ?? 0, 
        ], $code);
    }

    public function jsonifyException(\Exception $e, $code)
    {
        if (\method_exists($e, 'errors')){
            $error = $e->errors();
        }
        else{
            $error = $e->getMessage();
        }
        return $this->jsonResponse(['status'=>false, 'error'=>$error], $code);
    }
}