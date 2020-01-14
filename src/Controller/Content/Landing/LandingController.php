<?php
namespace App\Controller\Content\Landing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class LandingController extends AbstractController{

    /**
    * @Route("/", name="landing")
    */
    public function landingAction() {

        $testString = "Ich bin ein Test-String!";

        $testArray = ["eintrag1", "eintrag2", 12345, null, "Badewanne", false, true, 0, ""];

        return $this->render('content/landing/landing.html.twig', [
            "testString" => $testString,
            "testArray" => $testArray,
        ]);
    }



}