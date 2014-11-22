<?php

require __DIR__ . '/../bootstrap.php';

class RoomsModelTest extends TestBase
{

    public function testGetAll()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertTrue(is_array($rooms->getAll()));
    }

    public function testGetAllDates()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertTrue(is_array($rooms->getAllDates()));
    }

    public function testGetRoomUseForDates1()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertTrue(is_array($rooms->getRoomUseForDates(array())));
    }

    public function testGetRoomUseForDates2()
    {
        $rooms = new RoomsModel(new DB);
        $return = $rooms->getRoomUseForDates(array('2010-30-30'));
        $this->assertTrue(is_array($return));
        $this->assertTrue(isset($return['2010-30-30']));
        $this->assertTrue(empty($return['2010-30-30']));
    }

    public function testGetRoomUseForDates3()
    {
        $rooms = new RoomsModel(new DB);
        $return = $rooms->getRoomUseForDates(array('2009-10-30'));
        $this->assertTrue(is_array($return));
        $this->assertTrue(isset($return['2009-10-30']));
    }

    public function testGetRoomUseForDates4()
    {
        $rooms = new RoomsModel(new DB);
        $return = $rooms->getRoomUseForDates(array('2009-10-30', '2009-04-20'));
        $this->assertTrue(is_array($return));
        $this->assertTrue(isset($return['2009-10-30']));
        $this->assertTrue(isset($return['2009-04-20']));
    }

    public function testGetRoomUseForDates5()
    {
        $rooms = new RoomsModel(new DB);
        $return = $rooms->getRoomUseForDates(array('2009-04-09', '2009-04-10'));
        $this->assertTrue(is_array($return));
    }

    public function testGetLokaleAfviklinger1()
    {
        $rooms = new RoomsModel(new DB);
        $ef = new EntityFactory(new DB);
        $lokale = $ef->create('Lokaler');
        $result = $rooms->getLokaleAfviklinger($lokale);
        $this->assertTrue(is_array($result));
        $this->assertTrue(empty($result));
    }

    public function testGetLokaleAfviklinger2()
    {
        $rooms = new RoomsModel(new DB);
        $ef = new EntityFactory(new DB);
        $lokaler = $ef->create('Lokaler')->findAll();
        if (isset($lokaler[0]))
        {
            $result = $rooms->getLokaleAfviklinger($lokaler[0]);
            $this->assertTrue(is_array($result));
        }
    }

    public function testCreate1()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertFalse($rooms->create(new RequestVars(array())));
    }

    public function testCreate2()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertFalse($rooms->create(new RequestVars(array(
            'kan_bookes'  => 'test2',
        ))));
    }

    public function testCreate3()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertFalse($rooms->create(new RequestVars(array(
            'beskrivelse'  => 'test2',
        ))));
    }

    public function testCreate4()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertFalse($rooms->create(new RequestVars(array(
            'omraade'  => 'test2',
        ))));
    }

    public function testCreate5()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertFalse($rooms->create(new RequestVars(array(
            'omraade'     => 'test2',
            'beskrivelse' => 'test2',
            'skole'       => 'test2',
            'kan_bookes'  => 'test2',
        ))));
    }

    public function testCreate6()
    {
        $rooms = new RoomsModel(new DB);
        $this->assertFalse($rooms->create(new RequestVars(array(
            'omraade'     => 'test2',
            'beskrivelse' => 'test2',
            'skole'       => 'test2',
            'kan_bookes'  => 'ja',
            'id'          => 1,
        ))));
    }

    public function testCreate7()
    {
        $rooms = new RoomsModel(new DB);
        $room = $rooms->create(new RequestVars(array(
            'omraade'     => 'test2',
            'beskrivelse' => 'test3',
            'skole'       => 'test4',
            'kan_bookes'  => 'ja',
        )));
        $this->assertTrue($room instanceof DBObject);
        $this->assertTrue($room->isLoaded());
        $this->assertTrue($room->omraade == 'test2');
        $this->assertTrue($room->beskrivelse == 'test3');
        $this->assertTrue($room->skole == 'test4');
        $this->assertTrue($room->kan_bookes == 'ja');
        $room->delete();
    }

    public function testEdit1()
    {
        $rooms = new RoomsModel(new DB);
        $room = $rooms->create(new RequestVars(array(
            'omraade'     => 'test2',
            'beskrivelse' => 'test3',
            'skole'       => 'test4',
            'kan_bookes'  => 'ja',
        )));
        $this->assertTrue($room instanceof DBObject);
        $this->assertTrue($room->isLoaded());

        $this->assertTrue($rooms->edit($room, new RequestVars(array(
            'lokale' => array(
                'omraade'     => 'test5',
                'beskrivelse' => 'test6',
                'skole'       => 'test7',
                'kan_bookes'  => 'nej',
            ),
        ))));

        $this->assertTrue($room->omraade == 'test5');
        $this->assertTrue($room->beskrivelse == 'test6');
        $this->assertTrue($room->skole == 'test7');
        $this->assertTrue($room->kan_bookes == 'nej');
        $room->delete();
    }

    public function testEdit2()
    {
        $rooms = new RoomsModel(new DB);
        $room = $rooms->create(new RequestVars(array(
            'omraade'     => 'test2',
            'beskrivelse' => 'test3',
            'skole'       => 'test4',
            'kan_bookes'  => 'ja',
        )));
        $this->assertTrue($room instanceof DBObject);
        $this->assertTrue($room->isLoaded());

        $this->assertFalse($rooms->edit($room, new RequestVars(array(
            'lokale' => array(
                'omraade'     => 'test5',
                'beskrivelse' => 'test6',
                'skole'       => 'test7',
                'kan_bookes'  => 'bleah',
            ),
        ))));
        $room->delete();
    }

    public function testEdit3()
    {
        $rooms = new RoomsModel(new DB);
        $ef = new EntityFactory(new DB);
        $room = $ef->create('Lokaler');
        $this->assertTrue($room instanceof DBObject);
        $this->assertFalse($room->isLoaded());

        $this->assertFalse($rooms->edit($room, new RequestVars(array(
            'lokale' => array(
                'omraade'     => 'test5',
                'beskrivelse' => 'test6',
                'skole'       => 'test7',
                'kan_bookes'  => 'bleah',
            ),
        ))));
    }

    public function testDelete1()
    {
        $rooms = new RoomsModel(new DB);
        $room = $rooms->create(new RequestVars(array(
            'omraade'     => 'test2',
            'beskrivelse' => 'test3',
            'skole'       => 'test4',
            'kan_bookes'  => 'ja',
        )));
        $this->assertTrue($room instanceof DBObject);
        $this->assertTrue($room->isLoaded());

        $this->assertTrue($rooms->deleteRoom($room));
        $this->assertFalse($room->isloaded());
    }

    public function testDelete2()
    {
        $rooms = new RoomsModel(new DB);
        $ef = new EntityFactory(new DB);
        $room = $ef->create('Lokaler');
        $this->assertTrue($room instanceof DBObject);
        $this->assertFalse($room->isLoaded());

        $this->assertFalse($rooms->deleteRoom($room));
        $this->assertFalse($room->isloaded());
    }
}
