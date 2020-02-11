<?php
namespace App\Controller\Content\TreeProfil;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class TreeProfilController extends AbstractController{

    /**
    * @Route("/BaumProfil", name="treeProfil")
    */
    public function treeProfilAction() {

        return $this->render('content/treeProfil/treeProfil.html.twig', []);
    }
}