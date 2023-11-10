<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinmaxType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/showDB', name: 'showDB')]
    public function showDB(AuthorRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req): Response
    {
        $author = $authorRepository->findAll();
        $form=$this->createForm(MinmaxType::class);
        $form->handleRequest($req);

        if($form->isSubmitted()){
            $min=$form->get('min')->getData();
            $max=$form->get('max')->getData();
            $author=$authorRepository->minmax($min,$max);

            return $this->renderForm('author/showDB.html.twig', [
                
                'auth' => $author,
                'f' => $form           
            ]);
        }

        return $this->renderForm('author/showDB.html.twig', [
            'auth' => $author,
            'f' => $form 
        ]);
    }

    #[Route('/addAuthor', name: 'addAuthor')]
    public function addAuthor(AuthorRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req): Response
    {
        $m=$managerRegistry->getManager();
        $author=new Author();
        $form=$this->createForm(AuthorType::class,$author);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $m->persist($author);
            $m->flush();
            
            return $this->redirectToRoute('showDB');
        }
        return $this->renderForm('author/addAuthor.html.twig', [
            'add' => $form
        ]);
    }

    #[Route('/editAuthor/{id}', name: 'editAuthor')]
    public function editAuthor(AuthorRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req, $id): Response
    {
        $m=$managerRegistry->getManager();
        $findid=$authorRepository->find($id);
        $form=$this->createForm(AuthorType::class,$findid);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $m->persist($findid);
            $m->flush();

            return $this->redirectToRoute('showDB');
        }
        return $this->renderForm('author/editAuthor.html.twig', [
            'edit' => $form
        ]);
    }

    #[Route('/deleteAuthor/{id}', name: 'deleteAuthor')]
    public function deleteAuthor(AuthorRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req, $id): Response
    {
        $m=$managerRegistry->getManager();
        $findid=$authorRepository->find($id);
        $m->remove($findid);
        $m->flush();
        return $this->redirectToRoute('showDB');
    }

    #[Route('/showdbzero', name: 'showdbzero')]
    public function showdbzero(AuthorRepository $authorRepository): Response
    {

        $author = $authorRepository->findAll();
        $supprimerzerobook = $authorRepository->supprimerzerobook();
        return $this->render('author/showdbzeo.html.twig', [
            'supprimerzerobook' => $supprimerzerobook,
            'auth' => $author
            
        ]);
    }
}
