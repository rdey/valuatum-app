<?php

namespace Redeye\ValuatumClient;

class WsseAuthHeader extends \SoapHeader
{
    protected $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    public function __construct($user, $pass)
    {
        // Initializing namespaces
        $ns_soap = 'http://schemas.xmlsoap.org/soap/envelope/';
        $ns_wsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $ns_wsu = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
        $password_type = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';

        // Creating WSS identification header using SimpleXML
        $root = new \SimpleXMLElement('<root/>');

        $security = $root->addChild('wsse:Security', null, $ns_wsse);
//        $security->addAttribute('soapns:mustUnderstand', '1', $ns_soap);

        $usernameToken = $security->addChild('wsse:UsernameToken', null, $ns_wsse);
        $usernameToken->addChild('wsse:Username', $user, $ns_wsse);
        $usernameToken->addAttribute('wsu:Id', 'UsernameToken-1', $ns_wsu);

        $password = $usernameToken->addChild('wsse:Password', $pass, $ns_wsse);
        $password->addAttribute('Type', $password_type);

        // Recovering XML value from that object
        $root->registerXPathNamespace('wsse', $ns_wsse);
        $full = $root->xpath('/root/wsse:Security');
        $auth = $full[0]->asXML();

        $temp = new \SoapVar($auth, XSD_ANYXML);

        parent::__construct($this->wss_ns, 'Security', $temp, true);
    }
}
