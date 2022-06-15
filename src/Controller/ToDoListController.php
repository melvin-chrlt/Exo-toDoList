<?php

namespace App\Controller;

use App\Entity\ToDoList;
use App\Form\ToDoListType;
use App\Repository\ToDoListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ToDoListController extends AbstractController
{
    //ADD LIST
    #[Route('/toDoListAdd', name: 'app_to_do_list_add')]
    public function add(Request $request, ToDoListRepository $toDoListManager): Response
    {
        $toDoList = new ToDoList(); //déclaration d'une nouvelle instance de mon entité (ajout d'une ligne dans la bdd)
        $form = $this->createForm(ToDoListType::class, $toDoList); //$this = AbsratController (boîte à outil)
        $form->handleRequest($request); // vérifie si chaque champ est bon
        
        if($form->isSubmitted() && $form->isValid()){
            // $entity->persist($toDoList);
            // $entity->flush();
            $toDoListManager->add($toDoList);
            // $this->addFlash('success', 'La liste a bien été ajoutée');
            return $this->redirectToRoute('app_to_do_list_all');
        }
        
        return $this->render('toDoList/add.html.twig', ['form' => $form->createView()]);
    }

    // SEE ALL LISTS
    #[Route('/', name: 'app_to_do_list_all')]
    public function seeAll(ToDoListRepository $toDoListManager): Response
    {
        $entities = $toDoListManager->findAll();
        
        return $this->render('toDoList/all.html.twig', ['entities' => $entities]);
    }

    // SHOW A DETAIL LIST
    #[Route('/toDoListDetail/{id<\d+>}', name:'app_detail_list')]
    public function show(ToDoList $toDoList)
    {
        return $this->render('toDoList/detail.html.twig', [
            'toDoList' => $toDoList,
        ]);
    }

    //  DELETE LIST
    #[Route('/delete/{id<\d+>}', name: 'app_to_do_list_del')]
    public function delete(ToDoList $entity, ToDoListRepository $toDoListManager): Response
    {
        $toDoListManager->remove($entity);
        // $this->addFlash('success', 'La liste a bien été supprimée.');
        return $this->redirectToRoute('app_to_do_list_all');
    }

    // EDIT LIST    
    #[Route('/edit/{id<\d+>}', name:'app_to_do_list_edit')]
    public function edit(EntityManagerInterface $em, Request $request, ToDoList $entity)
    {
        $form = $this->createForm(OffresType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            // $this->addFlash('success', 'La liste a bien été modifiée.');
            return $this->redirectToRoute('app_to_do_list_all');
        }
        
        return $this->renderForm('toDoList/edit.html.twig', [
            // 'entity' => $entity,
            'form' => $form,
        ]);
    }
}
