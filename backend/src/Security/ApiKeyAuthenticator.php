<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        $path = $request->getPathInfo();
        // No autenticar la doc de la API ni health
        if (str_starts_with($path, '/api/doc')) {
            return false;
        }
        if ($path === '/api/doc.json') {
            return false;
        }
        if ($path === '/api/health') {
            return false;
        }
        return str_starts_with($path, '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY');
        if (!$apiKey) {
            throw new AuthenticationException('No API Key provided');
        }
        // Usar getenv para mayor compatibilidad en producción
        $validKey = getenv('API_KEY') ?: 'testkey';
        if ($apiKey !== $validKey) {
            throw new AuthenticationException('Invalid API Key');
        }
        return new SelfValidatingPassport(new UserBadge('api-user'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Permitir la petición
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response(json_encode(['message' => $exception->getMessageKey()]), 401, ['Content-Type' => 'application/json']);
    }
} 