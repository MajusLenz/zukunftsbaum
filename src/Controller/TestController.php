<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class TestController extends AbstractController{
    /**
    * @Route("/testdb", name="test_db")
    */
    public function testdbAction() {
        // only for development
        if($_ENV["APP_ENV"] !== "dev") {
            throw new NotFoundHttpException();
        }

        $number = random_int(0, 100);

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();

        $information = new TreeInformation();
        $information->setName("luckyNumber");
        $information->setValue($number);

        $tree = new Tree();
        $tree->addInformation($information);

        $entityManager->persist($tree);
        $entityManager->persist($information);
        $entityManager->flush();

        $trees = $doctrine->getRepository(Tree::class)->findAll();

        if (!$trees) {
            throw $this->createNotFoundException(
                'No trees found'
            );
        }

        return $this->render('testdb.html.twig', [
            'luckyNumber' => $number,
            "trees" => $trees
        ]);
    }

    /**
     * @Route("/testweb", name="test_web")
     */
    public function testwebAction() {
        // only for development
        if($_ENV["APP_ENV"] !== "dev") {
            throw new NotFoundHttpException();
        }

        $number = random_int(0, 100);

        return $this->render('testweb.html.twig', [
            'luckyNumber' => $number,
        ]);
    }
}