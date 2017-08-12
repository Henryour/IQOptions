<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 13.08.17
 * Time: 0:58
 */

namespace Tests\Unit;


use Tests\TestCase;

class PushNumbersTest extends TestCase
{
    public function testPushNumber()
    {
        $number = (string)mt_rand(10,100000);
        $response = $this->get('/api/push-number/'.$number.'?_mock=1');
        $response->assertJsonFragment(['in_amqp' => [$number]]);
    }

    public function testPushArray()
    {
        $number1 = (string)mt_rand(10,100000);
        $number2 = (string)mt_rand(100000,1000000000);
        $response = $this->post('/api/push-number?_mock=1', ['numbers' => [$number1, $number2]]);
        $response->assertJsonFragment(['in_amqp' => [$number1, $number2]]);
    }
}