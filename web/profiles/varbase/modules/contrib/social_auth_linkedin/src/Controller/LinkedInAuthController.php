<?php

namespace Drupal\social_auth_linkedin\Controller;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\social_api\Plugin\NetworkManager;
use Drupal\social_auth\Controller\OAuth2ControllerBase;
use Drupal\social_auth\SocialAuthDataHandler;
use Drupal\social_auth\User\UserAuthenticator;
use Drupal\social_auth_linkedin\LinkedInAuthManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Returns responses for Social Auth LinkedIn routes.
 */
class LinkedInAuthController extends OAuth2ControllerBase {

  /**
   * LinkedInAuthController constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\social_api\Plugin\NetworkManager $network_manager
   *   Used to get an instance of social_auth_linkedin network plugin.
   * @param \Drupal\social_auth\User\UserAuthenticator $user_authenticator
   *   Manages user login/registration.
   * @param \Drupal\social_auth_linkedin\LinkedInAuthManager $linkedIn_manager
   *   Used to manage authentication methods.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Used to access GET parameters.
   * @param \Drupal\social_auth\SocialAuthDataHandler $data_handler
   *   The Social Auth data handler.
   */
  public function __construct(MessengerInterface $messenger,
                              NetworkManager $network_manager,
                              UserAuthenticator $user_authenticator,
                              LinkedInAuthManager $linkedIn_manager,
                              RequestStack $request,
                              SocialAuthDataHandler $data_handler) {

    parent::__construct('Social Auth LinkedIn', 'social_auth_linkedin', $messenger, $network_manager, $user_authenticator, $linkedIn_manager, $request, $data_handler);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('plugin.network.manager'),
      $container->get('social_auth.user_authenticator'),
      $container->get('social_auth_linkedin.manager'),
      $container->get('request_stack'),
      $container->get('social_auth.data_handler')
    );
  }

  /**
   * Response for path 'user/login/linkedin/callback'.
   *
   * LinkedIn returns the user here after user has authenticated.
   */
  public function callback() {

    // Checks if authentication failed.
    if ($this->request->getCurrentRequest()->query->has('error')) {
      $this->messenger->addError($this->t('You could not be authenticated.'));

      return $this->redirect('user.login');
    }

    /* @var \League\OAuth2\Client\Provider\LinkedInResourceOwner|null $profile */
    $profile = $this->processCallback();

    // If authentication was successful.
    if ($profile !== NULL) {

      // Gets (or not) extra initial data.
      $data = $this->userAuthenticator->checkProviderIsAssociated($profile->getId()) ? NULL : $this->providerManager->getExtraDetails();

      $name = $profile->getFirstName() . ' ' . $profile->getLastName();

      // If user information could be retrieved.
      return $this->userAuthenticator->authenticateUser($name, $profile->getEmail(), $profile->getId(), $this->providerManager->getAccessToken(), $profile->getImageurl(), $data);
    }

    return $this->redirect('user.login');
  }

}
