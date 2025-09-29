<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/book')]
class BookController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private FileUploader $fileUploader) {}

    #[Route('/', name: 'book_index')]
    public function index(): Response
    {
        $books = $this->getUser() ? $this->getUser()->getBooks() : [];
        return $this->render('book/index.html.twig', ['books' => $books]);
    }

    #[Route('/new', name: 'book_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImage')->getData();
            if ($coverFile) {
                $filename = $this->fileUploader->upload($coverFile);
                $book->setCoverImage($filename);
            }

            $book->setUser($this->getUser());
            $this->em->persist($book);
            $this->em->flush();

            $this->addFlash('success', 'Livre créé avec succès !');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'book_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Book $book, Request $request): Response
    {
        if ($book->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce livre.');
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImage')->getData();
            if ($coverFile) {
                $filename = $this->fileUploader->upload($coverFile);
                $book->setCoverImage($filename);
            }

            $this->em->flush();
            $this->addFlash('success', 'Livre mis à jour avec succès !');
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', ['form' => $form->createView(), 'book' => $book]);
    }

    #[Route('/{id}/delete', name: 'book_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Book $book, Request $request): Response
    {
        if ($book->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce livre.');
        }

        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $this->em->remove($book);
            $this->em->flush();
            $this->addFlash('success', 'Livre supprimé avec succès !');
        }

        return $this->redirectToRoute('book_index');
    }
}
