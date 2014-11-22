<?php

require __DIR__ . '/../bootstrap.php';

class EntityFactoryTest extends TestBase
{

    public function testBadInput1()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->setExpectedException('EntityException');
        $ef->create();
    }

    public function testBadInput2()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->setExpectedException('EntityException');
        $ef->create(false);
    }

    public function testBadInput3()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->setExpectedException('EntityException');
        $ef->create('blah blah');
    }

    public function testAfviklinger()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Afviklinger')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testAfviklingerMultiblok()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('AfviklingerMultiblok')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testAktiviteter()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Aktiviteter')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testBrugerKategorier()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('BrugerKategorier')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagere()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Deltagere')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereArrangoerer()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereArrangoerer')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereGDSTilmeldinger()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereGDSTilmeldinger')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereGDSVagter()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereGDSVagter')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereIndgang()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereIndgang')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereMadtider()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereMadtider')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereTilmeldinger()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereTilmeldinger')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereUngdomsskole()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereUngdomsskole')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testDeltagereWear()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('DeltagereWear')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testGDS()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('GDS')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testGDSVagter()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('GDSVagter')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testHold()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Hold')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testIndgang()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Indgang')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testLogItem()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('LogItem')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testLokaler()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Lokaler')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testMad()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Mad')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testMadtider()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Madtider')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testPladser()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Pladser')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testPrivilege()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Privilege')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testRole()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Role')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testRolePrivilege()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('RolePrivilege')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testSMSLog()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('SMSLog')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testTodoItems()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('TodoItems')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testUser()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('User')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testUserRole()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('UserRole')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testVideoer()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Videoer')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testWear()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('Wear')));
        $this->assertTrue($r instanceof DbObject); 
    }

    public function testWearPriser()
    {
        $db = new DB;
        $ef = new EntityFactory($db);
        $this->assertTrue(is_object($r = $ef->create('WearPriser')));
        $this->assertTrue($r instanceof DbObject); 
    }
}
