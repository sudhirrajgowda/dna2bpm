<?php
/**
 * This libray load submodules and apply bindings for replaced functions
 * ldap_user_plugin
 * ldap_group_plugin
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 */
class Mysql_plugin {

    function __construct() {
        //parent::__construct();
        $ci = & get_instance();
        if ($ci) {
            $ci->load->library('user/mysql_user_plugin');
            $ci->user = $ci->mysql_user_plugin;
        /*
            $ci->load->config('user/ldap');
            if ($ci->config->item('ldap_use_groups')) {
                $ci->load->library('user/ldap_group_plugin');
                $ci->group = $ci->ldap_group_plugin;
            }
         *
         */
        }
    }

    function apply() {
        return true;
    }

    

}
