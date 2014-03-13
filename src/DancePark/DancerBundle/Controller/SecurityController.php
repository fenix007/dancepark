<?php
namespace DancePark\DancerBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends BaseController
{
    public function getUser()
    {
        if (null === $token = $this->container->get('security.context')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function loginAction(Request $request)
    {
        if (!$this->getUser()) {
            /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
            $session = $request->getSession();

            // get the error if any (works with forward and redirect -- see below)
            if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
            } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
                $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
                $session->remove(SecurityContext::AUTHENTICATION_ERROR);
            } else {
                $error = '';
            }

            if ($error){
                if ($error instanceof DisabledException) {
                    $error = 'Аккаунт заблокирован. Подтвердите email';
                } else {
                    $error = $error->getMessage();
                }
            }
            // last username entered by the user
            $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

            $csrfToken = $this->container->has('form.csrf_provider')
                ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;

            return $this->renderLogin(array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token' => $csrfToken,
            ));
        } else {
            return new Response();
        }
    }

    /**
     * {@inheritDoc}
     * @param array $data
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        $template = sprintf('DancerBundle:Security:login.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}