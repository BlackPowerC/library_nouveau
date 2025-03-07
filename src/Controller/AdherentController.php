<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Form\AdherentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdherentController extends AbstractController
{
    #[Route('/adherent', name: 'adherent')]
    public function index(): Response
    {
        return $this->render('adherent/index.html.twig', [
            'controller_name' => 'AdherentController',
        ]);
    }

    /**
     * Mettre à jour un livre existant.
     *
     *
     * @param EntityManagerInterface $em
     * @param Request $post
     * @param Adherent|null $adh
     * @return RedirectResponse|Response
     */
    #[Route('/adherent/edit/{adherent}', name: "edit_adh")]
    public function update(EntityManagerInterface $em, Request $post, Adherent $adh)
    {
        $form = $this->createForm(AdherentType::class, $adh);
        $form->handleRequest($post);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash("success", "Adhérent modifié avec succès");

            return $this->redirectToRoute("adherent");
        }

        return $this->render("adherent/adhEdit.html.twig", [
            "form" => $form->createView(),
            "adherent" => $adh
        ]);
    }

    /**
     * Enregistrer un livre existant.
     *
     * @param EntityManagerInterface $em
     * @param Request $post
     * @return RedirectResponse|Response
     */
    #[Route('/adherent/add', name: "add_adh")]
    public function edit(EntityManagerInterface $em, Request $post)
    {
        $adh = new Adherent();
        $form = $this->createForm(AdherentType::class, $adh);
        $form->handleRequest($post);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($adh);
            $em->flush();
            $this->addFlash("success", "Adhérent ajouté avec succès");
            return $this->redirectToRoute("adherent");
        }

        return $this->render("adherent/adhAdd.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * Supprimer un livre
     *
     * @param EntityManagerInterface $em
     * @param Request $delete
     * @param Adherent $adh
     * @return RedirectResponse
     */
    #[Route('adherent/delete/{adh}', name: "adh_del")]
    public function delete(EntityManagerInterface $em, Request $delete, Adherent $adh)
    {
        $csrfToken = $delete->request->get("_token");
        if ($this->isCsrfTokenValid("delete" . $adh->getId(), $csrfToken)) {
            $em->remove($adh);
            $em->flush();
            $this->addFlash("success", "Adherent supprimé");
        } else {
            $this->addFlash("warning", "Quelque chose s'est mal passé, veuillez reprendre");
        }

        return $this->redirectToRoute("adherent");
    }
}
