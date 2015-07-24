<?php
  include_once '../lib/system.php';
  
  $providerId = null;
  if (! isset($_GET['provider']) || ! is_numeric($_GET['provider'])) {
    header('Location: ../authenticate.page');
    exit;
  }
  
  // Load the authentication provider
  $providerId = intval($_GET['provider']);
  $provider = new \data\entities\AuthProvider();
  $provider->load($providerId);
  
  if (! $provider->validate()) {
    header('Location: ../authenticate.page');
    exit;
  }
  
  // Perform the authentication with the idP
  try {
    require ROOT . '/lib/hybridauth/Hybrid/Auth.php';
    $auth = new \Hybrid_Auth(ROOT . '/lib/config/config.HybridAuth.php');
    $authProvider = $auth->authenticate($provider->url);

    // Request user profile information from the authentication format
    $profile = $authProvider->getUserProfile();

    // Construct a 'fake' yet unique e-mail address if the IdP doesn't provide one.
    // This is a work-around for Twitter, among others.
    if (empty($profile->email)) {
      $profile->email = sha1( $profile->identifier );
    }

    // Authenticate the profile
    $nickname = trim( $profile->firstName.' '.$profile->lastName );
    if (empty($nickname)) {
      $nickname = null;
    }

    $token       = new \auth\AccessToken($provider->id, $profile->identifier);
    $credentials = \auth\Credentials::authenticate($provider->id, $profile->email, $token, $nickname);

    // Test authentication status
    if (! $credentials->permitted(new \auth\BasicAccessRequest())) {
      header('Location: ../authenticate.page');
      exit;
    }

    // Record the login attempt.
    $time       = time();
    $remoteAddr = $_SERVER['REMOTE_ADDR'];
    $account    = \auth\Credentials::current()->account();

    $stmt = \data\Database::instance()->connection()->prepare(
        'INSERT INTO `auth_logins` (`Date`, `IP`, `AccountID`) VALUES (?, ?, ?)'
    );
    $stmt->bind_param('isi', $time, $remoteAddr, $account->id);
    $stmt->execute();
    $stmt = null;

  } catch (\Exception $ex) {
    header('Location: ../authenticate.page?message=' . base64_encode($ex->getMessage()));
    exit;
  }

  header('Location: ../authenticate-complete.page');
  