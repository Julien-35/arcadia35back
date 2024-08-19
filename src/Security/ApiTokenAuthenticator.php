<?php
// src/Security/ApiTokenAuthenticator.php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\Passport\Passport;
use Symfony\Component\Security\Guard\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Guard\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function getCredentials(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');
        if (null === $authHeader) {
            return null;
        }

        return substr($authHeader, 7); // Remove "Bearer " from the start of the token
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if (null === $credentials) {
            return null;
        }

        // Assuming your User entity has a method to find by apiToken
        return $userProvider->loadUserByIdentifier($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true; // The token validation should be handled in getUser()
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null; // No redirect necessary
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Authentication failed', Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response('Authentication required', Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false; // Return false if you do not support "remember me"
    }
}
