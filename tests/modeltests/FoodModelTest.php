<?php

require __DIR__ . '/../bootstrap.php';

class FoodModelTest extends TestBase
{

    public function testGetFoodStats()
    {
        $mad = new FoodModel(new DB);
        $this->assertTrue(is_array($mad->getFoodStats()));
    }

    public function testGetAllFood()
    {
        $mad = new FoodModel(new DB);
        $this->assertTrue(is_array($mad->getAllFood()));
    }

    public function testCreateFood1()
    {
        $mad = new FoodModel(new DB);
        $this->assertFalse($mad->createFood(new RequestVars(array())));
    }

    public function testCreateFood2()
    {
        $mad = new FoodModel(new DB);
        $this->assertFalse($mad->createFood(new RequestVars(array(
            'pris'     => 'a',
            'kategori' => 'test',
        ))));
    }

    public function testCreateFood3()
    {
        $mad = new FoodModel(new DB);
        $this->assertFalse($mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => '',
        ))));
    }

    public function testCreateFood4()
    {
        $mad = new FoodModel(new DB);
        $this->assertFalse($mad->createFood(new RequestVars(array(
            'pris'     => '',
            'kategori' => '',
        ))));
    }

    public function testCreateFood5()
    {
        $mad = new FoodModel(new DB);
        $this->assertFalse($mad->createFood(new RequestVars(array(
            'pris'     => '',
            'kategori' => array(),
        ))));
    }

    public function testCreateFood6()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);
        $this->assertTrue($obj->pris == 10);
        $this->assertTrue($obj->kategori == 'test');
        $obj->delete();
    }

    public function testUpdateFood1()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);

        $this->assertFalse($mad->updateFood($obj, new RequestVars(array())));
        $obj->delete();
    }

    public function testUpdateFood2()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);

        $this->assertFalse($mad->updateFood($obj, new RequestVars(array(
            'pris'     => 'a',
            'kategori' => 'test',
        ))));
        $obj->delete();
    }

    public function testUpdateFood3()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);

        $this->assertFalse($mad->updateFood($obj, new RequestVars(array(
            'pris'     => '10',
            'kategori' => '',
        ))));
        $obj->delete();
    }

    public function testUpdateFood4()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);

        $this->assertFalse($mad->updateFood($obj, new RequestVars(array(
            'pris'     => '',
            'kategori' => '',
        ))));
        $obj->delete();
    }

    public function testUpdateFood5()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);

        $this->assertFalse($mad->updateFood($obj, new RequestVars(array(
            'pris'     => '',
            'kategori' => array(),
        ))));
        $obj->delete();
    }

    public function testUpdateFood6()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);

        $this->assertTrue($mad->updateFood($obj, new RequestVars(array(
            'pris'     => '100',
            'kategori' => 'testing',
        ))));
        $this->assertTrue($obj->pris == 100);
        $this->assertTrue($obj->kategori == 'testing');
        $obj->delete();
    }

    public function testUpdateFood7()
    {
        $mad = new FoodModel(new DB);
        $obj = $mad->createFood(new RequestVars(array(
            'pris'     => '10',
            'kategori' => 'test',
        )));
        $this->assertTrue($obj instanceof Mad);

        $ef = new EntityFactory(new DB);
        $newobj = $ef->create('Mad');
        $this->assertFalse($mad->updateFood($newobj, new RequestVars(array(
            'pris'     => '100',
            'kategori' => 'testing',
        ))));
        $obj->delete();
    }
}
