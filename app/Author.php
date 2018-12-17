<?php

namespace App;

class Author extends Base
{
    protected $table = "fb_author";

    public function authorSelect()
    {
        return $this->get()->toArray();
    }
}
