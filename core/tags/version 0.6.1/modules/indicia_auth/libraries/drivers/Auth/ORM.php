<?php defined('SYSPATH') or die('No direct script access.');
/**
 * ORM Auth driver.
 *
 * $Id: ORM.php 3273 2008-08-06 13:12:28Z Shadowhand $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Auth_ORM_Driver implements Auth_Driver {

  protected $config;

  // Session library
  protected $session;

  /**
   * Constructor. Loads the Session instance.
   *
   * @return  void
   */
  public function __construct(array $config)
  {
    // Load config
    $this->config = $config;

    // Load libraries
    $this->session = Session::instance();
  }

  public function logged_in($role)
  {
    $status = FALSE;

    // Checks if a user is logged in and valid
    if ( ! empty($_SESSION['auth_user']) AND is_object($_SESSION['auth_user'])
      AND ($_SESSION['auth_user'] instanceof User_Model) AND $_SESSION['auth_user']->loaded)
    {
      // Everything is okay so far
      $status = TRUE;

      if ( ! empty($role))
      {
        // Check that the user has the given role
        $core_role = (new Core_role_Model($role));
        $status = ($_SESSION['auth_user']->core_role_id == $core_role->id);
      }
    }

    return $status;
  }

  public function login($user, $password, $remember)
  {
    if ( ! is_object($user))
    {
      // Load the user
      $user = ORM::factory('user', $user);
    }

    // Two types of people have permissions to log in:
    // 1) Users with a valid core_role_id (There is only one type of core role - CoreAdmin)
    // 2) Users with Admin rights to at least one subsiduary website.
    // Users not in either of these groups (which may intersect) can not log into the CORE module
    // and can only log into the relevant subsiduary websites.
    
    $site_role = (new Site_role_Model('Admin'));
    $websites=ORM::factory('users_website')->where(
        array('user_id' => $user->id,
              'site_role_id' => $site_role->id))->find_all();

    // If the password in the database is null, then do not check the password
    if ((!is_null($user->core_role_id) OR ($websites->count() > 0))
        AND (is_null($user->password) OR ($user->password === $password)))
    {
      if ($remember === TRUE)
      {
        // Create a new autologin token
        $token = ORM::factory('user_token');

        // Set token data
        $token->user_id = $user->id;
        $token->expires = date("Ymd H:i:s", time() + $this->config['lifetime']);
        $token->save();

        // Set the autologin cookie
        cookie::set('authautologin', $token->token, $this->config['lifetime']);
      }

      // Finish the login
      $this->complete_login($user);

      return TRUE;
    }

    // Login failed
    return FALSE;
  }

  public function force_login($user)
  {
    if ( ! is_object($user))
    {
      // Load the user
      $user = ORM::factory('user', $user);
    }

    // Mark the session as forced, to prevent users from changing account information
    $_SESSION['auth_forced'] = TRUE;

    // Run the standard completion
    $this->complete_login($user);
  }

  public function auto_login()
  {
    if ($token = cookie::get('authautologin'))
    {
      // Load the token and user
      $token = ORM::factory('user_token', $token);

      if ($token->loaded AND $token->user->loaded)
      {
        if ($token->user_agent === sha1(Kohana::$user_agent))
        {
          // Save the token to create a new unique token
          $token->save();

          // Set the new token
          cookie::set('authautologin', $token->token, $token->expires - time());

          // Complete the login with the found data
          $this->complete_login($token->user);

          // Automatic login was successful
          return TRUE;
        }

        // Token is invalid
        $token->delete();
      }
    }

    return FALSE;
  }

  public function logout($destroy)
  {
    // Delete the autologin cookie if it exists
    cookie::get('authautologin') and cookie::delete('authautologin');

    if ($destroy === TRUE)
    {
      // Destroy the session completely
      Session::instance()->destroy();
    }
    else
    {
      // Remove the user object from the session
      unset($_SESSION['auth_user']);

      // Regenerate session_id
      $this->session->regenerate();
    }

    // Double check
    return ! isset($_SESSION['auth_user']);
  }

  public function password($user)
  {
    if ( ! is_object($user))
    {
      // Load the user
      $user = ORM::factory('user', $user);
    }

    return $user->password;
  }

  /**
   * Complete the login for a user by incrementing the logins and setting
   * session data: user_id, username, roles
   *
   * @param   object   user model object
   * @return  void
   */
  protected function complete_login(User_Model $user)
  {
    // Update the number of logins
//    $user->logins += 1;

    // Set the last login date
//    $user->last_login = time();

    // Save the user
//    $user->save();

    // Regenerate session_id
    $this->session->regenerate();

    // Store session data
    $_SESSION['auth_user'] = $user;
  }

} // End Auth_ORM_Driver Class