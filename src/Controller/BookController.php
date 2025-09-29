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

#[Route('/books')]
class BookController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, private FileUploader $fileUploader)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'book_index')]
    public function index(): Response
    {
        $books = $this->getUser()->getRoles()[0] === 'ROLE_ADMIN'
            ? $this->em->getRepository(Book::class)->findAll()
            : $this->em->getRepository(Book::class)->findBy(['user' => $this->getUser()]);

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/new', name: 'book_new')]
    public function new(Request $request): Response
    {
        $book = new Book();
        $book->setUser($this->getUser());

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImage')->getData();
            if ($coverFile) {
                $filename = $this->fileUploader->upload($coverFile);
                $book->setCoverImage($filename);
            }

            $book->getCreatedAt(new \DateTimeImmutable());
            $book->getUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($book);
            $this->em->flush();

            $this->addFlash('success', 'Livre créé avec succès !');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'book_edit')]
    public function edit(Request $request, Book $book): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $book);

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImage')->getData();
            if ($coverFile) {
                $filename = $this->fileUploader->upload($coverFile);
                $book->setCoverImage($filename);
            }

            $book->setUpdatedAt(new \DateTimeImmutable());

            $this->em->flush();
            $this->addFlash('success', 'Livre modifié avec succès !');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    #[Route('/{id}/delete', name: 'book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $book);

        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $this->em->remove($book);
            $this->em->flush();
            $this->addFlash('success', 'Livre supprimé avec succès !');
        }

        return $this->redirectToRoute('book_index');
    }
}
