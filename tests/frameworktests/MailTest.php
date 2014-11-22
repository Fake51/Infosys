<?php

require __DIR__ . '/../bootstrap.php';

class MailTest extends TestBase
{

    public function testSendSimple()
    {
        $mail = new Mail('peter.e.lind@gmail.com', 'peter.e.lind@gmail.com', 'test email 1', 'Weeeeee!');
        $this->assertTrue($mail->send());
    }

    public function testSendMultipleTo()
    {
        $mail = new Mail('peter.e.lind@gmail.com', array('peter.e.lind@gmail.com'), 'test email 2', 'Weeeeee!');
        $this->assertTrue($mail->send());
    }

    public function testSendBadFrom()
    {
        $this->setExpectedException('MailException');
        $mail = new Mail('blah blah', 'fake51@localhost', 'test email', 'Weeeeee!');
    }
}
