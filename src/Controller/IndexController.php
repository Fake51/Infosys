<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/{path}", name="index", requirements={"path"="^(?!api).*"})
     */
    public function index()
    {
        return $this->render('index/index.html.twig');
    }
}
