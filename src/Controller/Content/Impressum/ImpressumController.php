<?php
namespace App\Controller\Content\Impressum;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class ImpressumController extends AbstractController{

    /**
    * @Route("/Impressum", name="impressum")
    */
    public function impressumAction() {

        return $this->render('content/impressum/impressum.html.twig', []);
    }
}