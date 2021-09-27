<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $repo): Response
    {   
        return $this->render('pins/index.html.twig', ['pins' => $repo->findAll()]);
    }
    /* les versions moins simplifiées :
    public function index(EntityManagerInterface $em): Response
    {
        premier exemple: 

        $pin = new Pin;
        $pin->setTitle('Title 4');
        $pin->setDescription('Description 4');

        $em = $this->getDoctrine()->getManager();
        plus besoin de ça grace à l'injection de dépendances
        
        $em->persist($pin);
        $em->flush();
        dump($pin);

        deuxième exemple: 

        $repo = $em->getRepository(Pin::class);
        $pins = $repo ->findAll();

        on remplace 
        return $this->render('pins/index.html.twig', ['pins' => $pins]);
        par :
        return $this->render('pins/index.html.twig', compact('pins'));

    } */


    // Les formulaires :
    /**
     * @Route("/pins/create", methods="GET|PATCH|POST", name="app_pins_create")
     */

    // Formulaire 1, methode classique laborieuse

    /* public function create(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')){
            $data = ($request->request->all());
            // dd($data);

            $pin = new Pin;
            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);
            $em->persist($pin);
            $em->flush();

            // return $this->redirect($this->generateUrl('app_home'));
            // ou :
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pins/create.html.twig');
    } */

    // Formulaire 2, methode avec 
    public function create(Request $request, EntityManagerInterface $em): Response
    {
            $form = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('submit', TextType::class, ['label' => 'Create Pin'])
            ->getForm()
        ;

        return $this->render('pins/create.html.twig', [
            'monFormulaire' => $form->createView()
        ]);
    }
}
