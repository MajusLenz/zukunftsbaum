<?php
namespace App\Controller\Content\Finder;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class FinderController extends AbstractController{

    /**
    * @Route("/Baumfinder", name="finder")
    */
    public function finderAction() {

        return $this->render('content/finder/finder.html.twig', []);
    }
}