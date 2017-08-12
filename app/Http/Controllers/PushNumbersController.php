<?php

namespace App\Http\Controllers;

use App\AMQP;
use Illuminate\Support\Facades\Input;
use Mookofe\Tail\Tail;

class PushNumbersController extends Controller
{
    const JSON_PARAM_NUMBERS = 'numbers';
    const JSON_PARAM_MOCK    = '_mock';

    const SPEC_FIELD_RESULT = 'result';
    const SPEC_FIELD_DATA   = 'data';
    const SPEC_FIELD_METHOD = 'method';
    const SPEC_FIELD_AMQP   = 'in_amqp';

    protected $mockAmqp;
    protected $isDebug;

    public function __construct()
    {
        $this->isDebug = Input::get(self::JSON_PARAM_MOCK) == 1;
    }


    public function pushAsNumber($number)
    {
        return $this->pushNumbersSpec(
            $this->validateAsNumbers([$number]) && $this->pushEnvelope([$number]),
            $number,
            __METHOD__
        );
    }

    public function pushAsNumbersArray()
    {
        $numbers = Input::get(self::JSON_PARAM_NUMBERS);
        return $this->pushNumbersSpec(
            $this->validateAsNumbers($numbers) && $this->pushEnvelope($numbers),
            $numbers,
            __METHOD__
        );
    }

    protected function validateAsNumbers($numbers)
    {
        if(!$numbers) {
            throw new \InvalidArgumentException('Please, give me a number!');
        }

        foreach($numbers as $number) {
            if(!is_numeric($number)) {
                throw new \InvalidArgumentException('Number must be a number!');
            }
        }

        return true;
    }

    protected function pushEnvelope(array $data)
    {
        $amqp = new Tail();

        foreach($data as $item) {
                $this->mockAmqp[] = (string)$item;
                if(!$this->isDebug)
                    $amqp->add(AMQP::QUEUE_NAME_NUMBERS, $item);
        }

        return true;
    }

    protected function pushNumbersSpec($result, $data, $method = __METHOD__)
    {
        $answer = [self::SPEC_FIELD_RESULT => $result];

        if($this->isDebug) {
            $answer += [
                self::SPEC_FIELD_DATA   => $data,
                self::SPEC_FIELD_METHOD => $method,
                self::SPEC_FIELD_AMQP   => $this->mockAmqp,
            ];
        }

        return $answer;
    }

}
