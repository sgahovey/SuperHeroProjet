<?php

namespace App\Controller;

use App\Entity\Pouvoir;
use App\Form\PouvoirType;
use App\Repository\PouvoirRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pouvoir')]
final class PouvoirController extends AbstractController
{
    #[Route(name: 'app_pouvoir_index', methods: ['GET'])]
    public function index(PouvoirRepository $pouvoirRepository): Response
    {
        return $this->render('pouvoir/index.html.twig', [
            'pouvoirs' => $pouvoirRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pouvoir_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pouvoir = new Pouvoir();
        $form = $this->createForm(PouvoirType::class, $pouvoir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pouvoir);
            $entityManager->flush();

            return $this->redirectToRoute('app_pouvoir_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pouvoir/new.html.twig', [
            'pouvoir' => $pouvoir,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pouvoir_show', methods: ['GET'])]
    public function show(Pouvoir $pouvoir): Response
    {
        return $this->render('pouvoir/show.html.twig', [
            'pouvoir' => $pouvoir,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pouvoir_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pouvoir $pouvoir, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PouvoirType::class, $pouvoir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pouvoir_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pouvoir/edit.html.twig', [
            'pouvoir' => $pouvoir,
            'form' => $form,
        ]);
    }

            #[Route('/{id}/heros', name: 'app_pouvoir_heros', methods: ['GET'])]
        public function heros(Pouvoir $pouvoir): Response
        {
            return $this->render('pouvoir/heros.html.twig', [
                'pouvoir' => $pouvoir,
                'super_heroes' => $pouvoir->getSuperHeroes(),
            ]);
        }

        
    #[Route('/{id}', name: 'app_pouvoir_delete', methods: ['POST'])]
    public function delete(Request $request, Pouvoir $pouvoir, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pouvoir->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pouvoir);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pouvoir_index', [], Response::HTTP_SEE_OTHER);
    }
}
