<?php
namespace App\Controller\Content\PAndNews;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class PAndNewsController extends AbstractController{

    /**
    * @Route("/Aktuelles_und_News", name="pAndNews")
    */
    public function pAndNewsAction() {

        return $this->render('content/pAndNews/pAndNews.html.twig', []);
    }
}