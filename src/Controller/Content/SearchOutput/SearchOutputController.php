<?php
namespace App\Controller\Content\SearchOutput;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class SearchOutputController extends AbstractController{

    /**
     * @Route("/SearchOutput", name="Output")
     */
    public function OutputAction() {


    }



}