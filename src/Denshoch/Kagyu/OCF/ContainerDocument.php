<?php

namespace Denshoch\Kagyu\OCF;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ContainerDocument
{
    /* 
    * object $var DOMDocument
    */
    protected $dom;

    /* 
    * object $fs Symfony FileSystem
    */
    protected $fs;

    /* 
    * string $rootDirPath full path of container root directory
    */
    protected $rootDirPath;

    /*
    * Construct
    * 
    * @param string $rootDirPath path of container root directory
    * @return void 
    */
    public function __construct($rootDirPath)
    {
        $this->fs = new Filesystem();

        $this->setRootDir($rootDirPath);

        $dom = new \DOMDocument;
        $dom->formatOutput = true;
        $dom->encoding = 'UTF-8';
        $this->dom = $dom;

    }

    /*
    * Set path of container root directory
    * 
    * @param string $rootDirPath path of container root directory
    * @return void 
    */
    public function setRootDir($rootDirPath)
    {
        if (!realpath($rootDirPath)) {
            throw new \Exception("Root directory not found"."\n", 1);
            
        } else {
            $this->rootDirPath = realpath($rootDirPath);
        }
    }

    /*
    * Initialize
    * 
    * @return void 
    */
    public function init($rootDirPath=null)
    {
        if(!is_null($rootDirPath)) {
            $this->setRootDir($rootDirPath);
        }

        $dom = new \DOMDocument;
        $dom->formatOutput = true;
        $dom->encoding = 'UTF-8';
        $this->dom = $dom;

        $container = $this->dom->createElement('container');
        $container->setAttribute("version","1.0");
        $container->setAttribute("xmlns","urn:oasis:names:tc:opendocument:xmlns:container");
        $this->dom->appendChild($container);

        $rootfiles = $this->dom->createElement('rootfiles');
        $container->appendChild($rootfiles);
    }

    /*
    * Scan root directory and add root file automatically
    * 
    * @return void 
    */
    public function scan()
    {
        $this->init();

        $finder = new Finder();
        $iterator = $finder
        ->files()
        ->name('/\.(opf|pdf)$/')
        ->in($this->rootDirPath);

        if(\count($iterator)==0){
            throw new \Exception("No root file found"."\n", 1);            
        }

        foreach ($iterator as $file) {
            $rootfileRealPath = $file->getRealpath();
            echo "Add root file: ".$rootfileRealPath."\n";
            $this->addRootFile($rootfileRealPath);
        }

    }

    /*
    * Load XML string
    * 
    * @param string $str XML String 
    * @return void 
    */
    public function loadXML($str)
    {
        $this->dom->loadXML($str);
    }

    /*
    * Load XML file
    * 
    * @param string $fileName XML file
    * @return void 
    */
    public function load($fileName)
    {
        $this->dom->load($fileName);
    }

    /*
    * Add root file
    * 
    * @param string $rootfilePath Root file path
    * @return void 
    */
    public function addRootFile($rootfilePath)
    {
        if(!$rootfileRealPath = realpath($rootfilePath)) {
            throw new \Exception("Root file not found"."\n", 1);
        }

        if(is_dir($rootfileRealPath)) {
             throw new \Exception("Root file should not be a director¥y"."\n", 1);
        }

        $media_type = $this->detectMediaType($rootfilePath);

        $rootfileRelativePath = preg_replace('/\/$/', '', $this->fs->makePathRelative($rootfileRealPath,$this->rootDirPath));

        $rootfilesList = $this->dom->getElementsByTagName('rootfiles');

        if(!$rootfilesList->length == 1) {
            // no rootfiles element
            throw new \Exception("There must be only one rootfiles element\n", 1);
        }

        $rootfiles = $rootfilesList->item(0);

        $rootfileList = $this->dom->getElementsByTagName('rootfile');

        foreach($rootfileList as $r) {
            if($r->getAttribute('full-path') == $rootfileRelativePath) {
                // root file is already exist
                echo "Rootfile is already added: ".$rootfileRelativePath."\n";
                return;
            }
        }

        $rootfile = $this->dom->createElement('rootfile');
        $rootfile->setAttribute("full-path",$rootfileRelativePath);
        $rootfile->setAttribute("media-type",$media_type);
        $rootfiles->appendChild($rootfile);
    }

    /*
    * Detect media type of root file from extension.
    * only opf and pdf can detect
    * 
    * @param string $fileName File name
    * @return string media type 
    */
    private function detectMediaType($fileName) {

        $array = explode('.', basename($fileName));
        $ext = end($array);

        if($ext == 'opf'){
            // EPUB package document
            $media_type = "application/oebps-package+xml";
        } elseif($ext == 'pdf') {
            // PDF
            $media_type = "application/pdf";
        } else {
            echo "Undable to detect media-type: ".$fileName."\n";
            $media_type = "application/octet-stream";
        }

        return $media_type;
    }

    /*
    * Save as XML String
    * 
    * @return string XML string
    */
    public function save()
    {
        $rootfileList = $this->dom->getElementsByTagName('rootfile');
        if($rootfileList->length == 0) {
            // no rootfile element
            throw new \Exception("There must be one or more rootfile elements"."\n", 1);
        }

        return $this->dom->saveXML();
    }
}
