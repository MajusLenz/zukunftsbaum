<?php
namespace App\Controller\Content\DataProtection;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class DataProtectionController extends AbstractController{

    /**
    * @Route("/Datenschutz", name="dataProtection")
    */
    public function dataProtectionAction() {

        return $this->render('content/dataProtection/dataProtection.html.twig', []);
    }
}