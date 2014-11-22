<?php

require __DIR__ . '/../bootstrap.php';

class VideoModelTest extends TestBase
{
    public function testGetAllVideos()
    {
        $video = new VideoModel(new DB);
        $this->assertTrue(is_array($video->getAllVideos()));
    }
}
