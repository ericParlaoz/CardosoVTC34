<?php

namespace App\Service;

class UniqueIdService
{
  //  private $uniqueId;

 // public function __construct(string $uniqueId)
 // {
 //     $this->uniqueId = $uniqueId;
 // }

    public function generateRandomID($length = 10): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
       $uniqueId = '';
        for ($i = 0; $i < $length; $i++) {
            $uniqueId .= $characters[rand(0, $charactersLength - 1)];
        }
        return $uniqueId;
    }


}