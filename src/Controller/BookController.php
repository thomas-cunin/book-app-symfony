<?php

namespace App\Controller;

use DateTime;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(BookRepository $repo): Response
    {
        $books = $repo->findAll();
        dump($books);

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

        /**
     * @Route("/book/{id}", name="oneBook")
     */
    public function show(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        dump($book);

        return $this->render('book/one_book.html.twig', [
            'book' => $book,
        ]);
    }
        /**
     * @Route("/book/add", name="addBook", priority=1)
     * @Route("/book/{id}/edit", name="editBook", priority=1)
     */
    public function edit(Book $book = null,EntityManagerInterface $manager, Request $request): Response
    {
        $editMode = true;
        if (!$book){
            $book = new Book();
            $editMode = false;
        }   dump($book);
        
        $form=$this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$editMode){
                $book->setCreatedAt(new DateTime('now'));
                $this->addFlash('success', 'Le livre "'.$book->getTitle() .'" a bien été ajouté ');
            }
            
            $manager->persist($book);
            $manager->flush();
            
            return $this->redirectToRoute('oneBook', ['id'=>$book->getId()]);
        }


        return $this->render('book/add_book.html.twig', [
            'form' => $form->createView(),
        ]);
    }

         /**
    * @Route("/book/{id}/remove", name="removeBook", priority=1)
    */
    public function remove(Book $book,EntityManagerInterface $manager): Response
    {
        $manager->remove($book);
        $manager->flush();


        return $this->redirectToRoute('home');
    }

}
