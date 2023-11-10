<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\SearchType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/showBook', name: 'showBook')]
    public function showBook(BookRepository $bookRepository, Request $req): Response
    {
        $book = $bookRepository->orderbyauthor();
        $nb_sci_books=$bookRepository->sommesciencefiction();
        //$book = $bookRepository->listepublie();
        $listedate=$bookRepository->listedate();
        $form=$this->createForm(SearchType::class);
        $form->handleRequest($req);

        if($form->isSubmitted()){
            $data=$form->get('id')->getData();
            $book=$bookRepository->searchbyref($data);

            return $this->renderForm('book/showBook.html.twig', [
                
                'book' => $book,
                'f' => $form, 
                'nb_sci_books' => $nb_sci_books,
                
            ]);
        }

        return $this->renderForm('book/showBook.html.twig', [
            'book' => $book,
            'f' => $form ,
            'nb_sci_books' => $nb_sci_books,
        ]);
    }

    #[Route('/addBook', name: 'addBook')]
    public function addBook(BookRepository $bookRepository, ManagerRegistry $managerRegistry, Request $req): Response
    {
        $m=$managerRegistry->getManager();
        $book=new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $m->persist($book);
            $m->flush();
            return $this->redirectToRoute('showBook');
        }
        return $this->renderForm('book/showAddBook.html.twig', [
            'add' => $form
        ]);
    }

    #[Route('/editBook/{id}', name: 'editBook')]
    public function editBook(BookRepository $bookRepository, ManagerRegistry $managerRegistry, Request $req, $id): Response
    {
        $m=$managerRegistry->getManager();
        $findid=$bookRepository->find($id);
        $form=$this->createForm(BookType::class,$findid);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $m->persist($findid);
            $m->flush();

            return $this->redirectToRoute('showBook');
        }
        return $this->renderForm('book/editBook.html.twig', [
            'edit' => $form
        ]);
    }

    #[Route('/deleteBook/{id}', name: 'deleteBook')]
    public function deleteBook(BookRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req, $id): Response
    {
        $m=$managerRegistry->getManager();
        $findid=$authorRepository->find($id);
        $m->remove($findid);
        $m->flush();
        return $this->redirectToRoute('showBook');
    }

    #[Route('/showDate', name: 'showDate')]
    public function showDate(BookRepository $bookRepository): Response
    {
        
        $book = $bookRepository->listedate();

        return $this->render('book/showDate.html.twig', [
            'book' => $book
        ]);
    }

}
