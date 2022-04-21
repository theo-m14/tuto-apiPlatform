<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostCountController extends AbstractController
{
    public function __construct(private PostRepository $postManager)
    {
        
    }

    public function __invoke(Request $request) : int
    {
        $onlineQuery = $request->get('online');
        $conditions = [];
        if($onlineQuery !== null){
            $conditions = ['online' => $onlineQuery === '1' ? true : false]; 
        }
        return $this->postManager->count($conditions);
    }
}
