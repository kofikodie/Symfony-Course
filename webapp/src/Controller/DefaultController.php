<?php

namespace App\Controller;

use App\Entity\SecurityUser;
use App\Form\RegistrationUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use stdClass;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\ServiceInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Events\VideoCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Form\VideoFormType;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

//use App\Services\ServiceInterface;


class DefaultController extends AbstractController
{

    public function __construct(EventDispatcherInterface $dispatcher){
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/default", name="default")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
/*    public function index( MyServices3 $myServices) //ServiceInterface $myServices)
    {
        $em = $this->getDoctrine()->getManager();
        $pdfs = $em->getRepository(Pdf::class)->findAll();
        $authors = $em->getRepository(Author::class)->findByIdWithPdf(1);
        $videos = $em->getRepository(Video::class)->findAll();
        //$myServices->someAction();

        dump($myServices->secSecond->someMethod());
        dump($pdfs,$authors,$videos);
        foreach ($authors->getFiles() as $file){
                dump($file->getFilename());
        }
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }*/

    public function index(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        /*         $video = new stdClass();
                $video->title = 'Funny movie';
                $video->category = 'funny';

                //$event = new VideoCreatedEvent($video);
                $event = new VideoCreatedEvent($video);
                $this->dispatcher->dispatch('video.created.event', $event);

                return $this->render('default/index.html.twig', [
                    'controller_name' => 'DefaultController'
                ]);*/


        $video = new Video();
        /*$video->setAuthor("kodie");
        $video->setFormat('mp4');
        $video->setDuration(365);
        $video->setCreatedAt(new \DateTime());
        $video->getFormat();*/
        //$video = $entityManager->getRepository(Video::class)->find(1);

        $form = $this->createForm(VideoFormType::class,$video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            //dump($form->getData());
            $file = $form->get('file')->getData();
            $fileName = sha1(random_bytes(14)).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('videos_directory'),
                $fileName
            );
            $video->setFile($fileName);
            $entityManager->persist($video);
            $entityManager->flush();
            return $this->redirect('/default');
        }
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/caching", name="cache")
     */

    public function caching(){

        $cache = new FilesystemAdapter();

        $posts = $cache->getItem('database.get_posts');
        if (!$posts->isHit()){
            $posts_from_db = ['post1', 'post2', 'post3', 'post4'];
            dump('connected with database.....');
            $posts->set(serialize($posts_from_db));
            $posts->expiresAfter(10);
            $cache->save($posts);
        }

        dump(unserialize($posts->get()));
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/emails", name="emails")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return Response
     */

    public function emails(Request $request, Swift_Mailer $mailer){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $message = (new Swift_Message('Hello world'))
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    array('name' => 'Kodie')
                ),
                'text/html'
            );

        $mailer->send($message);
        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    /**
     * @Route({"en":"/register", "it":"/registrazione"}, name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user = new SecurityUser();
        $form = $this->createForm(RegistrationUserType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordEncoder->encodePassword($user,$form->get('password')->getData()));
            $user->setEmail($form->get('email')->getData());
            $user->setRoles(['ROLE_ADMIN']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('home');
            //return $this->redirect('http://localhost/home');
        }
        return $this->render('default/register.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function home(){
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository(Video::class)->find(5);
        $this->denyAccessUnlessGranted('VIDEO_DELETED',$video);
        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    //@Security("user.getId() == video.getSecurityUser().getId()")
    /**
     * @Route("/admin/{id}/delete-video", name="delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Video $video
     * @return Response
     */
    public function delete(Request $request, UserPasswordEncoderInterface $passwordEncoder, Video $video)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(SecurityUser::class)->findAll();
        dump($users);
        dump($video);

        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
