<?php

namespace Denshoch\Kagyu\OCF;

use ZipArchive;
use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Symfony\Component\Filesystem\Filesystem;

class EpubArchiver
{

    /* 
    * @var string base64 encoded zip container with mimetype
    */
    protected $zipBase = '
            UEsDBAoAAAAAAChlWEBvYassFAAAABQAAAAIAAAAbWltZXR5cGVhcHBsaWNhdGlvbi9lcHViK3ppcFBLAQIeAwoAAAAAAChlWEBvYassFAAAABQAAAAIAAAAAAAAAAAAAACkgQAAAABtaW1ldHlwZVBLBQYAAAAAAQABADYAAAA6AAAAAAA=';

    /* 
    * @var absolute path of input directory
    */
    protected $inputDirPath;

    /* 
    * @var absolute path of output directory
    */
    protected $outputDirPath;

    /* 
    * @var basename of output file
    */
    protected $outputFileName;

    protected $ignoreReg = '/^(\.|Thumbs\.db|mimetype)/';

    /* 
    * @var object ymfony FileSystem
    */
    protected $fs;

    /*
    * Construct
    * 
    * @return string $outputFilePath  absolute path of output file
    */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /*
    * Build input directory as EPUB file
    * 
    * @return void 
    */
    public function build($inputDirPath, $outputFileName=null)
    {
        if(!$inputDirRealPath = realpath($inputDirPath)) {
            throw new \Exception("Input directory not found: ".$inputDirPath."\n", 1);
        }

        $this->inputDirPath = $inputDirRealPath;

        if(\is_null($outputFileName)) {
            $this->outputFileName = \basename($this->inputDirPath).".epub";
            $this->outputDirPath = \dirname($this->inputDirPath);
        } else {
            $this->outputFileName = \basename($outputFileName);
            
            if(!$outputDirRealPath = \realpath(dirname($outputFileName))) {
                throw new \Exception("Output directory not found".$outputDirRealPath."\n", 1);
            }
            $this->outputDirPath = $outputDirRealPath;
        }

        $metainfDirPath = $this->inputDirPath.DIRECTORY_SEPARATOR."META-INF";
        $containerDocPath = $metainfDirPath.DIRECTORY_SEPARATOR."container.xml";
        if(!\file_exists($metainfDirPath)) {
            \mkdir($metainfDirPath);
            $this->createContainerDocument($containerDocPath);
        } elseif(!file_exists($containerDocPath)) {
            $this->createContainerDocument($containerDocPath);
        }

        $outputFilePath = $this->outputDirPath.DIRECTORY_SEPARATOR.$this->outputFileName;

        $zip = new ZipArchive();

        \file_put_contents($this->outputDirPath.DIRECTORY_SEPARATOR.$this->outputFileName, \base64_decode($this->zipBase));

        $zip->open($outputFilePath);

        $iterator  = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($this->inputDirPath));
        foreach ($iterator as $item) {

            if(true === $item->isDir() || 1 == preg_match($this->ignoreReg, $item->getBasename())) {
                continue;
            }

            $name = $item->__toString();
            $localPath = \str_replace($this->inputDirPath.DIRECTORY_SEPARATOR, '', $name);
            if (false === $zip->addFile($name, $localPath)) {
                throw new \Exception('Cannot add file ' . $localPath . ' to zip archive');
            }
        } 

        $zip->close();
        unset($zip);

        return $outputFilePath;
    }

    private function createContainerDocument($filePath)
    {
        $doc = new ContainerDocument($this->inputDirPath);
        $doc->scan();
        $doc->save();
        \file_put_contents($filePath, $doc->save());
    }
}
