<?php

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testTemplate1()
    {
        $data = [
            'toto' => 'titi'
        ];
        $tpl = "{{toto}}";
        $this->assertEquals("titi", template($tpl, $data));
    }

    public function testTemplate2()
    {
        $data = [
            'toto' => 'titi'
        ];
        $tpl = "{{titi}}";
        $this->assertNull( template($tpl, $data));
    }

    public function testTemplate3()
    {
        $data = [
            'toto' => 'titi'
        ];
        $tpl = "{{tata}}";
        $this->assertEmpty( template($tpl, $data));
    }

    public function testDebugLite1(){
        $string = "toto";
        $expected = "\n______________________________/var/www/quinen_php-lib/tests/FunctionsTest.php.38\n'toto'\n";
        $this->expectOutputString($expected);
        \debug_lite($string);
    }

}