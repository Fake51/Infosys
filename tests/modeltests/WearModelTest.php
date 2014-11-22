<?php

require __DIR__ . '/../bootstrap.php';

class WearModelTest extends TestBase
{

    public function testGetWearBreakdown()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getWearBreakdown()));
    }

    public function testGetWearSizes()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getWearSizes()));
    }

    public function testGetAllWear()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getAllWear()));
    }

    public function testGetAllWearPrices()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getAllWearPrices()));
    }

    public function testGetDeltagereWithWearOrders1()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getDeltagereWithWearOrders()));
    }

    public function testGetDeltagereWithWearOrders2()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getDeltagereWithWearOrders(1)));
    }

    public function testGetDeltagereWithWearOrders3()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getDeltagereWithWearOrders(null, 'S')));
    }

    public function testGetDeltagereWithWearOrders4()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getDeltagereWithWearOrders(1, 'S')));
    }

    public function testGetDeltagereWithWearOrders5()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getDeltagereWithWearOrders(9.33, array())));
        $this->assertTrue(count($wear->getDeltagereWithWearOrders(9.33, array())) == 0);
    }

    public function testGetWearOrders1()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getWearOrders()));
    }

    public function testGetWearOrders2()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getWearOrders(1)));
    }

    public function testGetWearOrders3()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getWearOrders(null, 'S')));
    }

    public function testGetWearOrders4()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getWearOrders(1, 'S')));
    }

    public function testGetWearOrders5()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getWearOrders(9.33, array())));
        // should test here that the array would be empty, but mysql casts
        // 9.33 as 9, so it won't be. Hence, you get results from passing in
        // bad data ... not exactly ideal
    }

    public function testCreateWear1()
    {
        $wear = new WearModel(new DB);
        $this->assertFalse($wear->createWear(new RequestVars(array())));
    }

    public function testCreateWear2()
    {
        $wear = new WearModel(new DB);
        $obj = $wear->createWear(new RequestVars(array(
            'navn'        => 'test',
            'min_size'    => 'S',
            'max_size'    => 'S',
            'beskrivelse' => 'testing',
        )));
        $this->assertTrue($obj instanceof Wear);
        $this->assertTrue($obj->navn == 'test');
        $this->assertTrue($obj->beskrivelse == 'testing');
        $this->assertTrue($obj->size_range == 'S-S');
        $this->assertTrue($obj->isLoaded());
        $obj->delete();
    }

    public function testCreateWear3()
    {
        $wear = new WearModel(new DB);
        $this->assertFalse($wear->createWear(new RequestVars(array(
            'navn'      => 'test',
            'min_size'  => 'M',
            'max_size'  => 'S',
        ))));
    }

    public function testCreateWear4()
    {
        $wear = new WearModel(new DB);
        $this->assertFalse($wear->createWear(new RequestVars(array(
            'min_size'  => 'S',
            'max_size'  => 'M',
        ))));
    }

    public function testCreateWear5()
    {
        $wear = new WearModel(new DB);
        $this->assertFalse($wear->createWear(new RequestVars(array(
            'navn'      => 'test',
            'max_size'  => 'M',
        ))));
    }

    public function testCreateWear6()
    {
        $wear = new WearModel(new DB);
        $this->assertFalse($wear->createWear(new RequestVars(array(
            'navn'      => 'test',
            'min_size'  => 'M',
        ))));
    }

    public function testUpdateWear1()
    {
        $wear = new WearModel(new DB);
        $obj = $wear->createWear(new RequestVars(array(
            'navn'      => 'test',
            'min_size'  => 'M',
            'max_size'  => 'L',
        )));
        $this->assertTrue($obj instanceof Wear);

        $this->assertTrue($wear->updateWear($obj, new RequestVars(array(
            'navn'      => 'testing',
            'min_size'  => 'S',
            'max_size'  => 'XL',
        ))));
        $this->assertTrue($obj->navn == 'testing');
        $this->assertTrue($obj->size_range == 'S-XL');
        $obj->delete();
    }

    public function testUpdateWear2()
    {
        $wear = new WearModel(new DB);
        $obj = $wear->createWear(new RequestVars(array(
            'navn'      => 'test',
            'min_size'  => 'M',
            'max_size'  => 'L',
        )));
        $this->assertTrue($obj instanceof Wear);

        $this->assertFalse($wear->updateWear($obj, new RequestVars(array(
            'navn'      => 'testing',
            'max_size'  => 'S',
            'min_size'  => 'XL',
        ))));
        $obj->delete();
    }

    public function testUpdateWear3()
    {
        $wear = new WearModel(new DB);
        $obj = $wear->createWear(new RequestVars(array(
            'navn'      => 'test',
            'min_size'  => 'M',
            'max_size'  => 'L',
        )));
        $this->assertTrue($obj instanceof Wear);

        $this->assertFalse($wear->updateWear($obj, new RequestVars(array(
            'navn'      => 'testing',
            'min_size'  => 'XL',
        ))));
        $obj->delete();
    }

    public function testUpdateWear4()
    {
        $wear = new WearModel(new DB);
        $obj = $wear->createWear(new RequestVars(array(
            'navn'      => 'test',
            'min_size'  => 'M',
            'max_size'  => 'L',
        )));
        $this->assertTrue($obj instanceof Wear);

        $this->assertFalse($wear->updateWear($obj, new RequestVars(array(
            'navn'      => 'testing',
            'max_size'  => 'XL',
        ))));
        $obj->delete();
    }

    public function testGetAllParticipantCategories()
    {
        $wear = new WearModel(new DB);
        $this->assertTrue(is_array($wear->getAllParticipantCategories()));
    }
}
