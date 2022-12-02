<?php
/**
 * Stud.IP authentication against CAS Server
 *
 * @access   public
 * @author   Dennis Reil <dennis.reil@offis.de>
 * @package
 */

require_once 'lib/classes/cas/CAS_PGTStorage_Cache.php';
require_once 'lib/classes/cas/CAS_ServiceBaseUrl_Studip.php';

class StudipAuthCAS extends StudipAuthSSO
{
    public $host;
    public $port;
    public $uri;
    public $cacert;

    public $userdata;

    /**
     * Constructor
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        if (!isset($this->plugin_fullname)) {
            $this->plugin_fullname = _('CAS');
        }
        if (!isset($this->login_description)) {
            $this->login_description = _('für Single Sign On mit CAS');
        }
        if (Request::get('sso') === $this->plugin_name) {
            $base_url = new CAS_ServiceBaseUrl_Studip();

            if ($this->proxy) {
                URLHelper::setBaseUrl($GLOBALS['ABSOLUTE_URI_STUDIP']);
                phpCAS::proxy(CAS_VERSION_2_0, $this->host, $this->port, $this->uri, $base_url, false);
                phpCAS::setPGTStorage(new CAS_PGTStorage_Cache(phpCAS::getCasClient()));
                phpCAS::setFixedCallbackURL(URLHelper::getURL('dispatch.php/cas/proxy'));
            } else {
                phpCAS::client(CAS_VERSION_2_0, $this->host, $this->port, $this->uri, $base_url, false);
            }

            if (isset($this->cacert)) {
                phpCAS::setCasServerCACert($this->cacert);
            } else {
                phpCAS::setNoCasServerValidation();
            }
        }
    }

    /**
     * Return the current username.
     */
    function getUser()
    {
        return phpCAS::getUser();
    }

    /**
     * Validate the username passed to the auth plugin.
     * Note: This triggers authentication if needed.
     */
    function verifyUsername($username)
    {
        phpCAS::forceAuthentication();
        return $this->getUser();
    }

    function getUserData($key)
    {
        $userdataclassname = $this->user_data_mapping_class;
        if (!class_exists($userdataclassname)) {
            Log::error($this->plugin_name . ': no userdataclassname specified or found.');
            return;
        }
        // get the userdata
        if (empty($this->userdata)) {
            $this->userdata = new $userdataclassname();
        }
        return $this->userdata->getUserData($key, phpCAS::getUser());
    }

    function logout()
    {
        // do a global cas logout
        $base_url = new CAS_ServiceBaseUrl_Studip();
        phpCAS::client(CAS_VERSION_2_0, $this->host, $this->port, $this->uri, $base_url, false);
        phpCAS::logout();
    }
}
