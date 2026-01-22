<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DebugAuthController extends AbstractController
{
    #[Route('/debug-login', name: 'app_debug_login')]
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        $error = null;
        $success = null;
        $debugInfo = [];

        // 1. Check DB Connection
        try {
            $conn = $em->getConnection();
            $debugInfo['db_params'] = $conn->getParams();
            // Mask password
            if (isset($debugInfo['db_params']['password'])) {
                $debugInfo['db_params']['password'] = '******';
            }
            $debugInfo['db_host'] = $debugInfo['db_params']['host'] ?? 'unknown';
            $debugInfo['db_name'] = $debugInfo['db_params']['dbname'] ?? 'unknown';
        } catch (\Exception $e) {
            $error = "DB Connection Error: " . $e->getMessage();
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $doReset = $request->request->get('do_reset');

            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $error = "User not found in database ($email).";
            } else {
                $debugInfo['user_id'] = $user->getId();
                $debugInfo['stored_hash'] = $user->getPassword();
                
                // Test 1: Verify existing
                $isValid = $passwordHasher->isPasswordValid($user, $password);
                $nativeValid = password_verify($password, $user->getPassword());
                
                $debugInfo['verify_symfony'] = $isValid ? 'YES' : 'NO';
                $debugInfo['verify_native'] = $nativeValid ? 'YES' : 'NO';

                if ($isValid) {
                    $success = "Login Successful! The password is correct.";
                } else {
                    $error = "Invalid Password.";
                    
                    // Test 2: Force Reset if requested
                    if ($doReset) {
                        $newHash = $passwordHasher->hashPassword($user, $password);
                        $user->setPassword($newHash);
                        $em->flush();
                        $success = "Password FORCE RESET to '$password'. Hash: $newHash. Try logging in again.";
                        $error = null;
                    }
                }
            }
        }

        return $this->render('debug/login.html.twig', [
            'error' => $error,
            'success' => $success,
            'debug_info' => $debugInfo
        ]);
    }
}
