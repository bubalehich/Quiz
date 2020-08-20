<?php // src/Controller/IndexController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
	/**
     * @Route("/", name="index")
     */
	public function list()
    {
        return $this->render('index/index.html.twig',['ip'=>$_SERVER['REMOTE_ADDR']]);
    }
}
?>