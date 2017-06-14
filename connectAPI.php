<?php
require_once "./vendor/autoload.php";

use Curl\Curl;


const APIURL = "";
const APPKEY = "";
const DEVKEY = "";
const ISACUSERNAME = "";
const ISACPASSWORD = "";

const LOGINPATH = "/oauth/token";
const INSTANCEPATH = "/rest/instance";
const INSTANCERESIZEPATH = "/rest/instance";
const PACKAGEPATH = "/rest/package";


class connectAPI
{
    var $token = "";
    var $cookies = array();
    var $curl = null;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    /**
     * Authenticates against the ISAC API
     *
     * @return bool Success
     */
    public function authenticate()
    {
        $loginEndPoint = APIURL . LOGINPATH;
        $this->curl->post($loginEndPoint, array(
            'grant_type' => 'client_credentials',
            'client_id' => ISACUSERNAME,
            'client_secret' => ISACPASSWORD,
            'appKey' => APPKEY,
            'devKey' => DEVKEY,
        ));
        if ($this->curl->error) {
            return FALSE;
        }
        $loginRespone = $this->curl->response;
        if ($loginRespone == null) {
            return FALSE;
        }
        $this->token = $loginRespone->access_token;
        $responeCookies = $this->curl->responseCookies;
        foreach ($responeCookies as $responeCookieName => $responeCookieValue) {
            $cookie = new stdClass();
            $cookie->name = $responeCookieName;
            $cookie->value = $responeCookieValue;
            $this->cookies[] = $cookie;
        }
    }


    /**
     * Retrieves all instances
     *
     * @return bool|instance array
     */
    public function getInstances()
    {
        $instanceEndPoint = APIURL . INSTANCEPATH;
        $this->addAuthentication();
        $this->curl->get($instanceEndPoint, array());
        if ($this->curl->error) {
            return FALSE;
        }
        $instances = $this->curl->response;
        if ($instances == null) {
            return FALSE;
        }
        return $instances;
    }


    /**
     * Adds the authentication to the curl request
     */
    public function addAuthentication()
    {
        // adding the cookies
        foreach ($this->cookies as $cookie) {
            $this->curl->setCookie($cookie->name, $cookie->value);
        }
        // adding the authentication header
        $this->curl->setHeader("authorization", "bearer " . $this->token);
    }


    /**
     * Triggers the instance firewall create
     *
     * @param $instance instance object
     *
     * @return bool|null firewall create result
     */
    public function createFirewallRule($instance)
    {
        $firewallCreatePath = APIURL . PACKAGEPATH . "/" . $instance->package_id . "/firewall/" . $instance->instance_id;
        $this->addAuthentication();
        $this->curl->post($firewallCreatePath, array("source_ip" => "any", "target_port" => "all", "fw_rule" => "deny", "protocol" => "tcp"));

        if ($this->curl->error) {
            return FALSE;
        }

        $firewallRuleResult = $this->curl->response;
        if ($firewallRuleResult == null) {
            return FALSE;
        }

        return $firewallRuleResult;
    }

    /**
     * Triggers the instance resize
     *
     * @param $instance instance object with the new parameter (cpu/ram/storage)
     *
     * @return bool|null resize result/resized parameter
     */
    public function resizeInstance($instance)
    {
        $instanceResizeEndPoint = APIURL . INSTANCERESIZEPATH . "/" . $instance->instance_id;
        $this->addAuthentication();
        $this->curl->put($instanceResizeEndPoint, array("cpu" => $instance->cpu, "ram" => $instance->ram, "storage" => $instance->storage));
        if ($this->curl->error) {
            return FALSE;
        }
        $resizeResult = $this->curl->response;
        if ($resizeResult == null) {
            return FALSE;
        }
        return $resizeResult;
    }
}
