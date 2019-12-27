<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class AdminController extends AbstractController{
    /**
    * @Route("/admin", name="admin_index")
    */
    public function adminIndexAction() {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();

        $trees = $doctrine->getRepository(Tree::class)->findAll();

        return $this->render('admin/admin_index.html.twig', [
            "trees" => $trees
        ]);
    }

    /**
     * @Route("/adminDeleteTree/{id}", name="admin_delete_tree")
     */
    public function adminDeleteTreeAction($id) {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $treeRepository = $doctrine->getRepository(Tree::class);

        $tree = $treeRepository->find($id);

        if( !empty($tree) ) {
            $entityManager->remove($tree);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_index', [], 301);
    }

    /**
     * @Route("/adminNewTree/", name="admin_new_tree")
     */
    public function adminNewTreeAction() {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $treeRepository = $doctrine->getRepository(Tree::class);


        // TODO check params and add new tree to DB


        return $this->redirectToRoute('admin_index', [], 301);
    }
}