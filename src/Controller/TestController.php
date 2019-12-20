<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use DateTime;

class TestController extends AbstractController{
    /**
    * @Route("/testdb", name="test_db")
    */
    public function testdbAction() {
        $number = random_int(0, 100);

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();

        $tree = new Tree();
        $tree->setCreatedAt(new DateTime());
        $entityManager->persist($tree);
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
        $number = random_int(0, 100);

        return $this->render('testweb.html.twig', [
            'luckyNumber' => $number,
        ]);
    }
}