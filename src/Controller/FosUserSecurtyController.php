<?php
/**
 * Created by PhpStorm.
 * User: aydn33
 * Date: 28.03.2019
 * Time: 17:18
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;

class FosUserSecurtyController extends BaseSecurityController
{

    /**
     * Overriding login to add custom logic.
     */
    public function loginAction(Request $request)
    {
        if( $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){

            // IS_AUTHENTICATED_FULLY also implies IS_AUTHENTICATED_REMEMBERED, but IS_AUTHENTICATED_ANONYMOUSLY doesn't

            return new RedirectResponse($this->container->get('router')->generate('test_tree', array()));
            // of course you don't have to use the router to generate a route if you want to hard code a route
        }

        return parent::loginAction($request);
    }
}