<?php

require_once 'vendor/autoload.php';

/**
 * @group containerdocument
 */

class ContainerDocumentTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fixtureDir = __DIR__.DIRECTORY_SEPARATOR.'fixture'.DIRECTORY_SEPARATOR.'ContainerDocument';
        $this->document = new Denshoch\Kagyu\OCF\ContainerDocument($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory");
    }

    public function testFailerConstruct()
    {
        // no root directory
        try{
            $this->document = new Denshoch\Kagyu\OCF\ContainerDocument("/path/to/rootDirectory");
            $this->assertTrue(false);
        } catch(Exception $e) {
            echo "Catch Exception successfully: ".$e->getMessage();
            $this->assertTrue(true);
        }  
    }

    public function testFailerSave()
    {
        // no rootfile
        try{
            $this->document->save();
            $this->assertTrue(false);
        } catch(Exception $e) {
            echo "Catch Exception successfully: ".$e->getMessage();
            $this->assertTrue(true);
        }        
    }

    public function testFailerSetRootDir()
    {
        // no rootfile
        try{
            $this->document->setRootDir("/path/to/rootDirectory");
            $this->assertTrue(false);
        } catch(Exception $e) {
            echo "Catch Exception successfully: ".$e->getMessage();
            $this->assertTrue(true);
        }        
    }

    public function testFailerAddRootFile()
    {
        // file not found
        try{
            $this->document->addRootFile("/path/to/rootFile");
            $this->assertTrue(false);
        } catch(Exception $e) {
            echo "Catch Exception successfully: ".$e->getMessage();
            $this->assertTrue(true);
        }
    }

    public function testSaveOPFRoot()
    {
        $this->document->init();
        $this->document->addRootfile($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory".DIRECTORY_SEPARATOR."dummy.opf");
        $actual = $this->document->save();
        $expected = $this->fixtureDir.DIRECTORY_SEPARATOR."testSaveOPFRoot.xml";
        $this->assertXmlStringEqualsXmlFile(
          $expected, $actual);
    }

    public function testSavePDFRoot()
    {
        $this->document->init();
        $this->document->addRootfile($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory".DIRECTORY_SEPARATOR."dummy.pdf");
        $actual = $this->document->save();
        $expected = $this->fixtureDir.DIRECTORY_SEPARATOR."testSavePDFRoot.xml";
        $this->assertXmlStringEqualsXmlFile(
          $expected, $actual);
    }

    public function testSaveUnknownRoot()
    {
        $this->document->init();
        $this->document->addRootfile($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory".DIRECTORY_SEPARATOR."dummy.unknown");
        $actual = $this->document->save();
        $expected = $this->fixtureDir.DIRECTORY_SEPARATOR."testSaveUnknownRoot.xml";
        $this->assertXmlStringEqualsXmlFile(
          $expected, $actual);
    }

    public function testSaveDuplexRoot()
    {
        $this->document->init();
        $this->document->addRootfile($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory".DIRECTORY_SEPARATOR."dummy.opf");
        $this->document->addRootfile($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory".DIRECTORY_SEPARATOR."dummy.opf");
        $actual = $this->document->save();
        $expected = $this->fixtureDir.DIRECTORY_SEPARATOR."testSaveOPFRoot.xml";
        $this->assertXmlStringEqualsXmlFile(
          $expected, $actual);
    }

    public function testSaveMultipleRoot()
    {
        $this->document->init();
        $this->document->addRootfile($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory".DIRECTORY_SEPARATOR."dummy.opf");
        $this->document->addRootfile($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectory".DIRECTORY_SEPARATOR."dummy.pdf");
        $actual = $this->document->save();
        $expected = $this->fixtureDir.DIRECTORY_SEPARATOR."testSaveMultipleRoot.xml";
        $this->assertXmlStringEqualsXmlFile(
          $expected, $actual);
    }

    public function testFailerScan()
    {
        // no root file found
        $this->document->init($this->fixtureDir.DIRECTORY_SEPARATOR."RootDirectoryNoRoot");
        try {
            $this->document->scan();
            $this->assertTrue(false);
        } catch(Exception $e) {
            echo "Catch Exception successfully: ".$e->getMessage();
            $this->assertTrue(true);
        }
    }

    public function testScan()
    {
        $this->document->scan();
        $actual = $this->document->save();
        $expected = $this->fixtureDir.DIRECTORY_SEPARATOR."testSaveMultipleRoot.xml";
        $this->assertXmlStringEqualsXmlFile(
          $expected, $actual);
    }
}