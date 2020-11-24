<?php

namespace App\Controller\BackOffice;

use App\Entity\Animation;
use App\Form\AnimationType;
use App\Repository\AnimationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AnimationController
 * @package App\Controller\BackOffice
 * @Route("/admin/animation")
 */
class AnimationController extends AbstractController
{
    /**
     * @Route(name="animation_manage")
     * @param AnimationRepository $animationRepository
     * @return Response
     */
    public function manage(AnimationRepository $animationRepository): Response
    {
        $animations = $animationRepository->findAll();

        return $this->render('back_office/animation/manage.html.twig', [
            "animations" => $animations
        ]);
    }


    /**
     * @Route("/create", name="animation_create")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $animation = new Animation();
        $form = $this->createForm(AnimationType::class, $animation)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($animation);
            $entityManager->flush();
            $this->addFlash("success", "L'animation a été ajoutée avec succès !");

            return $this->redirectToRoute("animation_manage");
        }

        return $this->render('back_office/animation/create.html.twig',[
            'newAnimationForm'=> $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/update", name="animation_update")
     * @param Animation $animation
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function update(Animation $animation, Request $request, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(AnimationType::class, $animation)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($animation);
            $entityManager->flush();
            $this->addFlash("success", "L'animation a été modifiée avec succès !");

            return $this->redirectToRoute("animation_manage");
        }

        return $this->render('back_office/animation/update.html.twig',[
            'updateAnimationForm'=> $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/delete", name="animation_delete")
     * @param Animation $animation
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function delete(Animation $animation, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($animation);
        $entityManager->flush();
        $this->addFlash("success", "L'animation a été supprimée avec succès !");

        return $this->redirectToRoute("animation_manage");
    }

    /**
     * @Route("/{id}/publish", name="animation_publish")
     * @param Animation $animation
     * @return RedirectResponse
     */
    public function publish(Animation $animation) :RedirectResponse
    {

        if ($animation->getIsPublished() == true) {
            $animation->setIsPublished(false);
            $this->addFlash("warning", "L'animation a été dépubliée !");
        } else {
            $animation->setIsPublished(true);
            $this->addFlash("sucess", "L'animation a été publiée !");
        }
        return $this->redirectToRoute("animation_manage");
    }

}
