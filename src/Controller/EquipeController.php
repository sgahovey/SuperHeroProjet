<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/equipe')]
final class EquipeController extends AbstractController
{
    #[Route(name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $equipe->setEstActive(true); // Par défaut, une équipe est active
        $form = $this->createForm(EquipeType::class, $equipe, [
            'include_active' => true,  // Inclure le champ "estActive"
            'include_chef' => true,    // Inclure le champ "chef"
            'include_membres' => true, // Inclure le champ "membres"
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si le chef a le niveau d'énergie requis
            if ($equipe->getChef() && $equipe->getChef()->getNiveauEnergie() <= 80) {
                $this->addFlash('error', "Le chef doit avoir un niveau d'énergie supérieur à 80.");
                return $this->render('equipe/new.html.twig', [
                    'form' => $form->createView(),
                    'equipe' => $equipe,
                ]);
            }
    
            // Vérifier que les membres sont différents du chef
            foreach ($equipe->getMembres() as $membre) {
                if ($membre === $equipe->getChef()) {
                    $this->addFlash('error', "Le chef ne peut pas être un membre de l'équipe.");
                    return $this->render('equipe/new.html.twig', [
                        'form' => $form->createView(),
                        'equipe' => $equipe,
                    ]);
                }
            }
    
            // Vérifier le nombre de membres
            if (count($equipe->getMembres()) < 2 || count($equipe->getMembres()) > 5) {
                $this->addFlash('error', "Une équipe doit avoir entre 2 et 5 membres.");
                return $this->render('equipe/new.html.twig', [
                    'form' => $form->createView(),
                    'equipe' => $equipe,
                ]);
            }
    
            // Rendre les super-héros indisponibles
            $chef = $equipe->getChef();
            if ($chef) {
                $chef->setEstDisponible(false); // Rendre le chef indisponible
                $entityManager->persist($chef);
            }
    
            foreach ($equipe->getMembres() as $membre) {
                $membre->setEstDisponible(false); // Rendre chaque membre indisponible
                $entityManager->persist($membre);
            }
    
            // Sauvegarder l'équipe
            $entityManager->persist($equipe);
            $entityManager->flush();
    
            // Succès
            $this->addFlash('success', "L'équipe a été créée avec succès.");
            return $this->redirectToRoute('app_equipe_index');
        }
    
        // Afficher le formulaire
        return $this->render('equipe/new.html.twig', [
            'form' => $form->createView(),
            'equipe' => $equipe,
        ]);
    }
    

    


    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(EquipeType::class, $equipe, [
        'include_active' => true, // Inclure "estActive" dans le formulaire
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Validation du nombre de membres (entre 2 et 5)
        if (count($equipe->getMembres()) < 2 || count($equipe->getMembres()) > 5) {
            $this->addFlash('error', 'Une équipe doit avoir entre 2 et 5 membres.');
            return $this->render('equipe/edit.html.twig', [
                'equipe' => $equipe,
                'form' => $form,
                'membres' => $equipe->getMembres(),
                'chef' => $equipe->getChef(),
            ]);
        }

        // Validation du niveau d'énergie du chef (> 80)
        if ($equipe->getChef() && $equipe->getChef()->getNiveauEnergie() <= 80) {
            $this->addFlash('error', 'Le chef doit avoir un niveau d\'énergie supérieur à 80.');
            return $this->render('equipe/edit.html.twig', [
                'equipe' => $equipe,
                'form' => $form,
                'membres' => $equipe->getMembres(),
                'chef' => $equipe->getChef(),
            ]);
        }

        // Vérification que le chef n'est pas un membre
        if ($equipe->getChef() && $equipe->getMembres()->contains($equipe->getChef())) {
            $this->addFlash('error', 'Le chef ne peut pas être un membre de l\'équipe.');
            return $this->render('equipe/edit.html.twig', [
                'equipe' => $equipe,
                'form' => $form,
                'membres' => $equipe->getMembres(),
                'chef' => $equipe->getChef(),
            ]);
        }

        // Vérification qu'aucun héros ne soit dans plusieurs équipes
        foreach ($equipe->getMembres() as $membre) {
            foreach ($membre->getEquipes() as $autreEquipe) {
                if ($autreEquipe !== $equipe) {
                    $this->addFlash('error', sprintf('Le héros %s appartient déjà à une autre équipe.', $membre->getNom()));
                    return $this->render('equipe/edit.html.twig', [
                        'equipe' => $equipe,
                        'form' => $form,
                        'membres' => $equipe->getMembres(),
                        'chef' => $equipe->getChef(),
                    ]);
                }
            }
        }

        try {
            $entityManager->flush();
            $this->addFlash('success', 'L\'équipe a été modifiée avec succès.');
            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la modification de l\'équipe.');
        }
    }

    return $this->render('equipe/edit.html.twig', [
        'equipe' => $equipe,
        'form' => $form,
        'membres' => $equipe->getMembres(),
        'chef' => $equipe->getChef(),
    ]);
}



#[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
        try {
            // Rendre les membres de l'équipe disponible si ils ne sont pas dans d'autres équipes actives
            foreach ($equipe->getMembres() as $membre) {
                $autresEquipes = $entityManager->getRepository(Equipe::class)
                    ->createQueryBuilder('e')
                    ->join('e.membres', 'm')
                    ->where('m.id = :membreId')
                    ->andWhere('e.id != :equipeId')
                    ->andWhere('e.estActive = true')
                    ->setParameter('membreId', $membre->getId())
                    ->setParameter('equipeId', $equipe->getId())
                    ->getQuery()
                    ->getResult();

                // Si le membre n'est pas dans d'autres équipes actives, on le rend disponible
                if (count($autresEquipes) === 0) {
                    $membre->setEstDisponible(true);
                    $entityManager->persist($membre);
                }
            }

            // Vérifier le chef de l'équipe
            $chef = $equipe->getChef();
            if ($chef) {
                $autresEquipesChef = $entityManager->getRepository(Equipe::class)
                    ->createQueryBuilder('e')
                    ->where('e.chef = :chefId')
                    ->andWhere('e.id != :equipeId')
                    ->andWhere('e.estActive = true')
                    ->setParameter('chefId', $chef->getId())
                    ->setParameter('equipeId', $equipe->getId())
                    ->getQuery()
                    ->getResult();

                // Si le chef n'est pas dans d'autres équipes actives, on le rend disponible
                if (count($autresEquipesChef) === 0) {
                    $chef->setEstDisponible(true);
                    $entityManager->persist($chef);
                }
            }

            // Supprimer l'équipe
            $entityManager->remove($equipe);
            $entityManager->flush();

            $this->addFlash('success', 'L\'équipe a été supprimée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
}



}