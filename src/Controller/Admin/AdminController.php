<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        return $this->redirectToRoute('admin_index');
    }


    /**
     * @Route("/adminNewTree/", name="admin_new_tree", methods={"POST"})
     */
    public function adminNewTreeAction() {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();

        $request = Request::createFromGlobals();
        $postParams = $request->request;


        dump($postParams);


        $name = $postParams->get('name');
        $name = trim($name);

        if(empty($name)) {
            throw new HttpException(400, "Tree-Name must not be empty!");
        }

        $duplicateTree = $doctrine->getRepository(Tree::class)->findOneBy( ["name" => $name] );
        if($duplicateTree !== null) {
            throw new HttpException(400, "Tree-Name must be unique!");
        }

        $treeInformations = array();

        // search all tree-informations in post-params
        foreach($postParams as $key => $value) {
            $isInformation = strpos($key, "information") !== false;

            if($isInformation) {
                if ( empty($key) || empty($value) ) {
                    throw new HttpException(400, "Tree-Information Names and Values must not be empty!");
                }

                $isInformationName = strpos($key, "information-name-") !== false;

                if ($isInformationName) {
                    $informationName = trim($value);
                    $informationNumber = str_replace("information-name-", "", $key);

                    $informationValue = $postParams->get("information-value-$informationNumber");
                    $informationValue = trim($informationValue);

                    array_push($treeInformations, array("name" => $informationName, "value" => $informationValue));
                }
            }
        }


        // TODO PictureS



        // persist new tree
        $newTree = new Tree();
        $newTree->setName($name);

        foreach($treeInformations as $info) {
            $duplicateInformation = $doctrine->getRepository(TreeInformation::class)->findOneBy( [
                "name" => $info["name"],
                "value" => $info["value"]
            ]);
            if($duplicateInformation === null) {
                $newInformation = new TreeInformation();
                $newInformation->setName( $info["name"] );
                $newInformation->setValue( $info["value"] );

            }
            else{
                $newInformation = $duplicateInformation;
            }

            $newTree->addInformation($newInformation);
            $entityManager->persist($newInformation);
        }

        $entityManager->persist($newTree);
        $entityManager->flush();


        return $this->redirectToRoute('admin_index');
    }
}