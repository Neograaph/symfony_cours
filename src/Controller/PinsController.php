<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show")

     */
    public function show(Pin $pin): Response
    {
        // dd($pin);
        // Méthode sans raccourci
        // $pin = $repo->find($id);

        // if (!$pin){
        //     throw $this->createNotFoundException(' Pin #'. $id . ' not found !');
        // }

        // avec :
        
        return $this->render('pins/show.html.twig', compact('pin'));
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

    // Formulaire 2, methode avec form de composer
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        // possible de passer un objet ou un tableau
        // plus sévère sur un objet, tout les champs doivent avoir des setters/getters.
        $form = $this->createFormBuilder($pin)
            ->add('title', null, ['attr' => ['autofocus' => true]])
            ->add('description',null , ['attr' => ['rows' => 10, 'col' => 50]])
            // ->add('submit', SubmitType::class, ['label' => 'Create Pin'])
            // Bonnes pratiques : gérer le bouton submit au niveau de la vue et laisser symfony définir le type du champs auto
            // TextareaType::class ====> null
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 2 manières de faire. Créer $pin plus haut pour éviter le code suivant.
            // $data = $form->getData();
            // $pin = new Pin;
            // $pin->setTitle($data['title']);
            // $pin->setDescription($data['description']);

            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', [
            'monFormulaire' => $form->createView(),
        ]);
    }
}
