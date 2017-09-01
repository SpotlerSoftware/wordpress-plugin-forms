<?php
/*  

Copyright (c) 2012, MailPlus (email : info@mailplus.nl)
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once 'Zend/Rest/Client/Result.php';
require 'rest/Client.php';
require 'Zend/Oauth/Consumer.php';

function cmp_form($form1, $form2)
{
    return strcasecmp($form1->name, $form2->name);
}


class mailplus_forms_api
{
    private function get_client()
    {
        $options = get_option('mpforms_plugin_options');
        $config = array(
            'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
            'version' => '1.0',
            'signatureMethod' => "HMAC-SHA1",
            'consumerKey' => $options['mpforms_consumer_key'],
            'consumerSecret' => $options['mpforms_consumer_secret']
        );

        $token = new Zend_Oauth_Token_Access();
        $httpClient = $token->getHttpClient($config);

        $client = new Zend_Rest_Client($options['mpforms_api_url']);
        $client->setHttpClient($httpClient);
        return $client;
    }


    public function get_forms()
    {
        $client = $this->get_client();

        $result = $client->get("/integrationservice/form/list");

        $forms = null;
        foreach ($result as $form) {
            $forms[] = $form;
        }

        usort($forms, "cmp_form");
        return $forms;
    }

    public function get_form($formId, $posturl, $encId = null)
    {
        $options = get_option('mpforms_plugin_options');

        $outputFormat = 'XHTML1STRICT';
        if ($options['mpforms_htmlxhtml'] == 'html') {
            $outputFormat = 'HTML4STRICT';
        }

        $outputMode = 'TABLES';
        if ($options['mpforms_tablesdivs'] == 'divs') {
            $outputMode = 'DIV';
        }

        $client = $this->get_client();
        $client->postUrl($posturl);
        $client->outputFormat($outputFormat);
        $client->outputMode($outputMode);

        if ($encId) {
            $client->encId($encId);
        }

        return $client->get("/integrationservice/form/" . $formId);
    }

    public function post_form($formId, $posturl, $postvars)
    {
        $options = get_option('mpforms_plugin_options');

        $outputFormat = 'XHTML1STRICT';
        if ($options['mpforms_htmlxhtml'] == 'html') {
            $outputFormat = 'HTML4STRICT';
        }

        $outputMode = 'TABLES';
        if ($options['mpforms_tablesdivs'] == 'divs') {
            $outputMode = 'DIV';
        }

        $client = $this->get_client();

        $formdata = new SimpleXMLElement('<params></params>');

        $formdata->postUrl = $posturl;
        $formdata->outputMode = $outputMode;
        $formdata->outputFormat = $outputFormat;

        $params = $formdata->addChild("formParams");

        foreach ($postvars as $key => $value) {
            $entry = $params->addChild("entry");
            $entry->key = $key;
            $value_xml = $entry->addChild("value");
            if (is_array($value)) {
                foreach ($value as $currentVal) {
                    $value_xml->addChild("item", $currentVal);
                }
            } else {
                $value_xml->item = $postvars[$key];
            }

        }

        $response = $client->restPost("/integrationservice/form/result/" . $formId,
            $formdata->asXML(),
            "application/xml"
        );
        // $client->post does this for us, $client->restPost does not
        return new Zend_Rest_Client_Result($response->getBody());
    }

    public function getRequiredFiles()
    {
        foreach (get_included_files() as $file) {
            echo $file . "\r\n";
        }
    }
}
