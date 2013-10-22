<?php

require_once 'vendor/autoload.php';

/**
 * @group containerdocument
 */

class EpubArchiverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fixtureDir = __DIR__.DIRECTORY_SEPARATOR.'fixture'.DIRECTORY_SEPARATOR.'EpubArchiver';
        $this->archiver = new Denshoch\Kagyu\OCF\EpubArchiver();
    }

    public function testFailerBuildInputDirNotFound()
    {
        // input dir not found
        try{
            $inputDir = "/path/to/inputDir";
            $this->archiver->build($inputDir);
            $this->assertTrue(false);
        } catch(Exception $e) {
            echo "Catch Exception successfully: ".$e->getMessage();
            $this->assertTrue(true);
        }
    }

    public function testFailerBuildOutputDirNotFound()
    {
        // output dir not found
        try{
            $inputDir = $this->fixtureDir.DIRECTORY_SEPARATOR."OutputDirNotFound";
            $this->archiver->build($inputDir,"/path/to/outputDir/output.epub");
            $this->assertTrue(false);
        } catch(Exception $e) {
            echo "Catch Exception successfully: ".$e->getMessage();
            $this->assertTrue(true);
        }
    }

    public function testBuildNoMetainfDir()
    {
        try{
            $inputDir = $this->fixtureDir.DIRECTORY_SEPARATOR."NoMetainfDir";
            $metainfDir = $inputDir.DIRECTORY_SEPARATOR."META-INF";
            $containerDoc = $metainfDir.DIRECTORY_SEPARATOR."container.xml";
            $outputFile = $this->fixtureDir.DIRECTORY_SEPARATOR."NoMetainfDir.epub";
            $this->archiver->build($inputDir);
    
            $this->assertFileExists($metainfDir);
            $this->assertFileExists($containerDoc);
            $this->assertFileExists($outputFile);
    
            unlink($containerDoc);
            unlink($outputFile);
            rmdir($metainfDir);
        } catch(Exception $e) {
            unlink($containerDoc);
            unlink($outputFile);
            rmdir($metainfDir);
        }
    }

    public function testBuild()
    {
        $inputDir = $this->fixtureDir.DIRECTORY_SEPARATOR."Epub3";
        $outputFile = $this->fixtureDir.DIRECTORY_SEPARATOR."Epub3.epub";
        $this->archiver->build($inputDir);
        $this->assertFileExists($outputFile);
        unlink($outputFile);
    }
}