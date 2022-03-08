<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\EnchereRepository;
use App\Repository\EncherirRepository;
use App\Utils\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\Curl\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/getGagnant", name="getGagnant")
     */
    public function getGagnant(Request $request, EnchereRepository $enchereRepository, EncherirRepository $encherirRepository)
    {
        $postdata = json_decode($request->getContent());
        if (isset($postdata->Id))
            $id = $postdata->Id;
        else 
            Utils::ErrorMissingArguments();
        
        $enchere = $enchereRepository->findOneBy(['id' => $id]);
        $var = $encherirRepository->findGagnantEnchere($enchere);
        $response = new Utils;
        return $response->GetJsonResponse($request, $var);
    }

    /**
     * @Route("/api/getUserByMailAndPass",name="GetUserByMailAndPass")
     */
    public function GetUserByMailAndPass(Request $request, UserRepository $userRepository)
    {
       return json_decode($request->getContent());
        $postdata = json_decode($request->getContent());
        if (isset($postdata->email)|| isset($postdata->password)) {
            $email = $postdata->email;
            $password = $postdata->password;
        } else 
            Utils::ErrorMissingArguments();
        $var = $userRepository->findUserByEmailAndPass(['email' => $email],['password' => $password]);
        $response = new Utils;
        return $response->GetJsonResponse($request, $var);
    }

    /**
     * @Route("/api/postUser", name="postUser")
     */
    public function PostUser(Request $request, EntityManagerInterface $manager)
    {
        $postdata = json_decode($request->getContent());
        $user = new User();
        $user->setEmail($postdata->Email);
        $user->setPassword($postdata->Password);

        $user->setPseudo($postdata->Pseudo);
        $user->setphoto($postdata->Photo);



        $manager->persist($user);
        $manager->flush();

        $response = new Response($user->getId());
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
}