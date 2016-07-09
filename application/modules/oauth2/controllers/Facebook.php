<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * dna2
 * 
 * This class will handle all the hassle needed to login with a facebook account
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date   Jan 18, 2016
 */
include(APPPATH.'modules/oauth2/libraries/Autoload.php');
class Facebook extends MX_Controller {

    function __construct() {
        parent::__construct();
        //---base variables
        $this->base_url = base_url();
        $this->module_url = base_url() . $this->router->fetch_module() . '/';
        //----LOAD LANGUAGE
        $this->lang->load('library', $this->config->item('language'));
        $this->idu = $this->user->idu;
        $this->provider= new League\OAuth2\Client\Provider\Facebook([
        'clientId'     => '1700216486880211',
        'clientSecret' => 'a5cc554d00d96a06fb18526e8e9c9393',
        'redirectUri'  => $this->module_url.'facebook/landing',
        'graphApiVersion'   => 'v2.5',
        ]);
        ini_set('xdebug.var_display_max_depth',-1);
    }
    function Index(){
        $authUrl = $this->provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $this->provider->getState();
        redirect($authUrl);
        exit;
    }
    
    function landing(){
        $rs=$this->input->get();
        // Got an error, probably user denied access
        if(!empty($rs['error'])){
            echo $rs['error'];
        exit;
        }
        // var_dump($rs);
            // Try to get an access token (using the authorization code grant)

    // Optional: Now you have a token you can look up a users profile data
    try {
         $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $rs['code']
        ]);

        // We got an access token, let's now get the owner details
        $ownerDetails = $this->provider->getResourceOwner($token);
        // var_dump('$ownerDetails',$ownerDetails);
        // Use these details to create a new profile or authorize a newone
        // $user=$this->user->getbyfilter(array('facebookId'=>$ownerDetails->getid()));
        $user=$this->user->getbymailaddress(array($ownerDetails->getEmail()));
        
        if(!$user){
           if($this->config->item('allow_register')){
        //Register a new user
            $user['idu']=$this->user->genid();
            $user['name']=$ownerDetails->getFirstName();
            $user['lastname']=$ownerDetails->getLastName();
            $user['nick']=$ownerDetails->getEmail();
            $user['email']=$ownerDetails->getEmail();
            $user['auth']='facebook';
            $user['facebookId']=$ownerDetails->getid();
            $user['group']=array($this->config->item('groupUser'));
            $user['avatar']=$ownerDetails->getPictureUrl();
            $user['checkdate']=date('Y-m-d H:i:s');
            $user['facebookdata']=$ownerDetails->toArray();
            } else {
               $this->session->set_userdata('msg', 'noregister');
               redirect( base_url() . 'user/login');
            }
        } else{
        //---update avatar & user data
        $user=(array)$user;
        $user['avatar']=$ownerDetails->getPictureUrl();
        $user['facebookdata']=$ownerDetails->toArray();
        }
        
        $this->user->save($user);
        
        //---register if it has logged is
        $this->session->set_userdata('loggedin', true);
        //---register the user id
        $this->session->set_userdata('iduser', $user['idu']);
        //---register level string
        $redir = $this->session->userdata('redir');
        $redir = ($this->session->userdata('redir')) ? $this->session->userdata('redir') : base_url() . $this->config->item('default_controller');
        log_message('debug', 'Redirecting user:' . $this->session->userdata('iduser') . ' to:' . $redir);
        //---clear redir from session
        $this->session->unset_userdata('redir');
        //---clear msg from session
        $this->session->unset_userdata('msg');
        //---clear msgcode from session
        $this->session->unset_userdata('msgcode');
        redirect($redir);
        exit;

    } catch (Exception $e) {

        // Failed to get user details
        var_dump($e);
        exit('Something went wrong: ' . $e->getMessage());

    }

    // Use this to interact with an API on the users behalf
    // echo $token->accessToken;

    // Use this to get a new access token if the old one expires
    var_dump('token',$token);
    }
}
/**
 array (size=5)
  'state' => string 'q6iVFAodPEnxGYlxp80fgSd2CYbEEqJy' (length=32)
  'code' => string '4/5frkwStp65I4q9Na5cD7VOLtV7sdMxzWLcvZJpAc__0' (length=45)
  'authuser' => string '0' (length=1)
  'session_state' => string '5b555786a1005050929963b3d6aed06ad35e70e7..ae20' (length=46)
  'prompt' => string 'none' (length=4)
object(League\OAuth2\Client\Provider\GoogleUser)[40]
  protected 'response' => 
    array (size=5)
      'emails' => 
        array (size=1)
          0 => 
            array (size=1)
              'value' => string 'juanignacioborda@gmail.com' (length=26)
      'id' => string '113983509196521428627' (length=21)
      'displayName' => string 'Juan Ignacio Borda' (length=18)
      'name' => 
        array (size=2)
          'familyName' => string 'Borda' (length=5)
          'givenName' => string 'Juan Ignacio' (length=12)
      'image' => 
        array (size=1)
          'url' => string 'https://lh4.facebookusercontent.com/-VTMrwynw_Ps/AAAAAAAAAAI/AAAAAAAAA2U/UFLptSt6D0M/photo.jpg?sz=50' (length=98)
Hello Juan Ignacio!
string 'token' (length=5)
object(League\OAuth2\Client\Token\AccessToken)[51]
  protected 'accessToken' => string 'ya29.bgJHTdWCxnQdnd2m-QGBNvtKjlB9hF7tPvofhlYrKTo4H-ORBlqwK2nDpz2gTFWZ-pvT' (length=73)
  protected 'expires' => int 1453175919
  protected 'refreshToken' => null
  protected 'resourceOwnerId' => null
 */