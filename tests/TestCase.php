<?php

namespace Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        require_once './vendor/autoload.php';

        $dotenv = new Dotenv(__DIR__.'/..');
        $dotenv->load();
    }
}