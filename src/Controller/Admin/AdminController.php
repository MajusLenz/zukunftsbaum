<?php
namespace App\Controller\Admin;

use App\Entity\TreePicture;
use ErrorException;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function adminIndexAction(string $treePicsDir) {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();

        $trees = $doctrine->getRepository(Tree::class)->findAll();

        return $this->render('admin/admin_index.html.twig', [
            "trees" => $trees,
            "treePicsDir" => $treePicsDir . "/"
        ]);
    }


    /**
     * @Route("/adminDeleteTree/{id}", name="admin_delete_tree")
     */
    public function adminDeleteTreeAction($id, string $treePicsDirFull) {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $treeRepository = $doctrine->getRepository(Tree::class);

        $tree = $treeRepository->find($id);

        if( !empty($tree) ) {

            // delete tree-pics
            foreach ($tree->getPictures() as $pic) {
                try{
                    unlink($treePicsDirFull . "/" . $pic->getPath());
                }
                catch(ErrorException $e) {}

                $entityManager->remove($pic);
            }

            $entityManager->remove($tree);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_index');
    }


    /**
     * @Route("/adminNewTree/", name="admin_new_tree", methods={"POST"})
     */
    public function adminNewTreeAction(Request $request, string $treePicsDirFull) {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $postParams = $request->request;

        $name = $postParams->get('name');
        $name = trim($name);

        if(empty($name)) {
            throw new HttpException(400, "Tree-Name must not be empty!");
        }

        $duplicateTree = $doctrine->getRepository(Tree::class)->findOneBy( ["name" => $name] );
        if($duplicateTree !== null) {
            throw new HttpException(400, "Tree-Name must be unique!");
        }

        $newTree = new Tree();
        $newTree->setName($name);


        // search all tree-information-names and link them to their corresponding values
        $treeInformations = array();

        foreach($postParams as $key => $param) {
            $isInformation = strpos($key, "information") !== false;

            if($isInformation) {
                if ( empty($key) || empty($param) ) {
                    throw new HttpException(400, "Tree-Information Names and Values must not be empty!");
                }

                $isInformationName = strpos($key, "information-name-") !== false;

                if ($isInformationName) {
                    $informationName = trim($param);
                    $informationNumber = str_replace("information-name-", "", $key);

                    $informationValue = $postParams->get("information-value-$informationNumber");
                    $informationValue = trim($informationValue);

                    array_push($treeInformations, array("name" => $informationName, "value" => $informationValue));
                }
            }
        }

        // persist tree-informations
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


        // upload tree-pictures and persist their path
        $files = $request->files;
        $pictures = $files->get("pictures");

        foreach($pictures as $pic) {
            $filename = uniqid();
            $extension = $pic->guessExtension();
            $path = $filename . "." . $extension;
            $isImageType = in_array( strtolower($extension), array("png", "gif", "jpg", "jpeg", "webp") );

            if($isImageType) {
                $pic->move($treePicsDirFull, $path);
                $newTreePicture = new TreePicture();
                $newTreePicture->setPath($path);
                $newTree->addPicture($newTreePicture);
                $entityManager->persist($newTreePicture);
            }
        }


        $entityManager->persist($newTree);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }
}