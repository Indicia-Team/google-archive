<?php

/**
 * Implementation of hook_menu_default_menu_links().
 */
function indicia_quickstart_menu_default_menu_links() {
  $menu_links = array();

  // Exported menu link: primary-links:logout
  $menu_links['primary-links:logout'] = array(
    'menu_name' => 'primary-links',
    'link_path' => 'logout',
    'router_path' => 'logout',
    'link_title' => 'Logout',
    'options' => array(
      'attributes' => array(
        'title' => 'Log out of the website',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '0',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '50',
  );
  
  // Exported menu link: primary-links:user/register
  $menu_links['primary-links:user/register'] = array(
    'menu_name' => 'primary-links',
    'link_path' => 'user/register',
    'router_path' => 'user/register',
    'link_title' => 'Register',
    'options' => array(
      'attributes' => array(
        'title' => 'Register as a user of the website',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '0',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '49',
  );
  
  // Exported menu link: primary-links:user/login
  $menu_links['primary-links:user/login'] = array(
    'menu_name' => 'primary-links',
    'link_path' => 'user/login',
    'router_path' => 'user/login',
    'link_title' => 'Login',
    'options' => array(
      'attributes' => array(
        'title' => 'Login to the website',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '0',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '50',
  );
  
  // Translatables
  // Included for use with string extractors like potx.
  t('Logout');
  t('Register');
  t('Login');


  return $menu_links;
}
