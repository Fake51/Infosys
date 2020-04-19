<?php
declare(strict_types=1);


namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TokenAuthenticator constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null) : Response
    {
        $data = [
            'message' => 'Authentication required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request) : bool
    {
        return '/login' === $request->getRequestUri()
            && $request->isMethod('POST');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request) : ?array
    {
        $data = json_decode($request->getContent(), true) ?: [];

        if (isset($data['email']) && isset($data['password'])) {
            return [
                'email' => $data['email'],
                'password' => $data['password'],
            ];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider) : ?User
    {
        if (!$credentials) {
            return null;
        }

        return $userProvider->loadUserByUsername($credentials['email']);
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user) : bool
    {
        if (!$credentials) {
            return false;
        }

        if (password_verify($credentials['password'], $user->getPassword())) {
            $user->createApiToken();
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => 'Authentification failed'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        /** @var User $user */
        $user = $token->getUser();

        $data = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'token' => $user->getApiToken(),
        ];

        return new JsonResponse($data);
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}