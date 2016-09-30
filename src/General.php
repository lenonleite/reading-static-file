<?php
/**
 * Created by PhpStorm.
 * User: lenonleite
 * Date: 01/07/16
 * Time: 18:13
 */

namespace Aszone\ReadingStaticFile;

use Aszone\FakeHeaders\FakeHeaders;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Aszone\ReadingStaticFile\WordPress;

class General
{
    public $file;

    public $language;

    public $commandData;

    public $url;

    public $urlBaseExploit;

    public $folderSave;

    public $folderDownload;

    public function __construct($commandData)
    {

        $this->commandData = array_merge($this->defaultEnterData(), $commandData);
        //$this->folderDownload = __DIR__."/../../../../results/lfd/";

    }


    private function defaultEnterData()
    {
        $dataDefault['dork'] = false;
        $dataDefault['pl'] = false;
        $dataDefault['tor'] = false;
        $dataDefault['torl'] = false;
        $dataDefault['virginProxies'] = false;
        $dataDefault['proxyOfSites'] = false;

        return $dataDefault;
    }


    //stay
    public function checkLanguage($file){
        $isValid = preg_match("/<%@|<%|<\?php|<\?=|<\?/", $file, $m);
        if ($isValid) {
            switch ($m[0]) {
                case "<?php":
                    $result= "php";
                    break;
                case "<?=":
                    $result= "php";
                    break;
                case "<?":
                    $result= "php";
                    break;
                case "<%@":
                    $result= "asp";
                    break;
                case "<%":
                    $result= "asp";
                    break;
            }
            return $result;
        }

        return false;
    }

    //stay
    public function readFile($url)
    {
        $header = new FakeHeaders();
        try {
            $client = new Client(['defaults' => [
                'headers' => ['User-Agent' => $header->getUserAgent()],
                'proxy' => $this->commandData['tor'],
                'timeout' => 30,
                ],
            ]);
            $resultBody = $client->get($url)->getBody()->getContents();
            return $resultBody;
        } catch (\Exception $e) {
            echo '#';
        }

        return false;
    }

    //stay
    protected function checkIfFileSystem($body,$urlFile){


        $isValid = preg_match("/<%@|<%|<\?php|<\?=|<\?/", $body, $m);
        $validResult = preg_match("/.".$this->language.".*?(=|\/)(.+?)\.(".$this->language."|ini|inc|yml|env|html)/i", $urlFile,
            $m2);

        if($isValid or ( $validResult and ($m2[3]=="ini" or $m2[3]=="inc" or $m2[3]=="yml" or $m2[3]=="env"))){
            return true;
        }
        return false;
    }

    //stay
    protected function getLinks($body)
    {
        //var_dump($body);
        $crawler = new Crawler($body);
        $urls=array();
        $crawler->filter('a')->each(function (Crawler $node, $i) use(&$res) {
            $res[]= $node->attr('href');
        });
        $crawler->filter('area')->each(function (Crawler $node, $i) use(&$res) {
            $res[]= $node->attr('href');
        });
        if($res){
            foreach($res as $r){
                $urls[]=$this->generateExploitOfLinkInBody($r);
            }
        }


        return $urls;

    }


}
