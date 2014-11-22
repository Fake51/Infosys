<?php

require __DIR__ . '/../bootstrap.php';

class TodoModelTest extends TestBase
{

    public function testFindRole1()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $this->setExpectedException('DBException');
        $this->assertFalse($todo->findRole(''));
    }

    public function testFindRole2()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $this->assertTrue($todo->findRole('Admin') instanceof Role);
    }

    public function testFetchTodos1()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $ef = new EntityFactory($db);
        $role = $ef->create('Role');
        $this->assertTrue(is_array($todo->fetchTodos($role)));
    }

    public function testFetchTodos2()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $this->assertTrue(is_array($todo->fetchTodos($role)));
    }

    public function testAddNote1()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $ef = new EntityFactory($db);
        $role = $ef->create('Role');
        $this->assertFalse($todo->addNote('testnote', 'testnotebody', $role));
    }

    public function testAddNote2()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);
        $this->assertTrue($note->note == 'testnote');
        $this->assertTrue($note->note_body == 'testnotebody');
        $this->assertTrue($note->role_id == $role->id);
        $note->delete();
    }

    public function testUpdateNote1()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);

        $updated_note = $todo->updateNote('updated test', 'updated test body', 'ja', $note, $role);
        $this->assertTrue($updated_note instanceof TodoItems);
        $this->assertTrue($updated_note->note == 'updated test');
        $this->assertTrue($updated_note->note_body == 'updated test body');
        $this->assertTrue($updated_note->done == 'ja');
        $this->assertTrue($updated_note->role_id == $role->id);
        $updated_note->delete();
    }

    public function testUpdateNote2()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);

        $this->assertFalse($todo->updateNote(false, 'updated test body', 'ja', $note, $role));
        $note->delete();
    }

    public function testUpdateNote3()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);

        $this->assertFalse($todo->updateNote('', 'updated test body', 'ja', $note, $role));
        $note->delete();
    }

    public function testUpdateNote4()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);

        $this->assertFalse($todo->updateNote('test', 'update text', 9, $note, $role));
        $note->delete();
    }

    public function testUpdateNote5()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);

        $ef = new EntityFactory($db);
        $note = $ef->create('TodoItems');
        $this->assertFalse($todo->updateNote('test', 'update text', 'ja', $note, $role));
        $note->delete();
    }

    public function testUpdateNote6()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);

        $ef = new EntityFactory($db);
        $role = $ef->create('Role');
        $this->assertFalse($todo->updateNote('test', 'update text', 'ja', $note, $role));
        $note->delete();
    }

    public function testUpdateNote7()
    {
        $db = new DB;
        $todo = new TodoModel($db);
        $role = $todo->findRole('Admin');
        $note = $todo->addNote('testnote', 'testnotebody', $role);
        $this->assertTrue($note instanceof TodoItems);

        $ef = new EntityFactory($db);
        $role = $ef->create('Role')->findById(2);
        $this->assertFalse($todo->updateNote('test', 'update text', 'ja', $note, $role));
        $note->delete();
    }
}
