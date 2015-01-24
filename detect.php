<?php

function getJoomlaVersion($site)
{
    // Add http prefix if missing
    if (strpos($site,'http://') === false)
    {
        $site = 'http://'.$site;
    }

    // Get the number value from the <version> tag in the XML file
    $dom = new DOMDocument;
    $url = $site . '/administrator/manifests/files/joomla.xml';
    libxml_use_internal_errors(true);

    // Check file exists using CURL
    function XMLexists($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $getinfo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $getinfo;
    }

    $exists = XMLexists($url);

    if( $exists )
    {
        $dom->load($url);
        $versions = $dom->getElementsByTagName('version');

        foreach ($versions as $version) 
        {
            return $version->nodeValue;
        }
    }
    else 
    {
        // Get TinyMCE Version 
        function getTinyMCEversion($site)
        {
            $tinymce = $site . '/plugins/editors/tinymce/tinymce.xml';      
            libxml_use_internal_errors(true);

            $exists = XMLexists($tinymce);

            if( $exists )
            {
                $dom->load($tinymce);
                $vTag = $dom->getElementsByTagName('version');

                foreach ($vTag as $tag) 
                {
                    return $tag->nodeValue;
                }
            }
        }

        $mce = getTinyMCEversion($site);

        if($mce)
        {
            // Base Joomla version on the TinyMCE version
            switch ($mce)
            {
                case '3.5.6':
                    $joomla = '3.0.0 - 3.1.6';
                    break;
                case '4.0.10':
                    $joomla = '3.2.0 - 3.2.1';
                    break;
                case '4.0.12':
                    $joomla = '3.2.2';
                    break;
                case '4.0.18':
                    $joomla = '3.2.3 - 3.2.4';
                    break;
                case '4.0.22':
                    $joomla = '3.3.0';
                    break;
                case '4.0.28':
                    $joomla = '3.3.1 - 3.3.6';
                    break;  
                default:
                    $joomla = '3.x';
            }

            return $joomla;
        }
        else 
        {
            return 'Unknown';
        }
    }   
}