<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/


namespace Tygh\Addons\CommerceML\Tests\Unit;


use Tygh\Addons\CommerceML\CommandBus;
use Tygh\Common\OperationResult;
use Tygh\Tests\Unit\ATestCase;

class CommandBusTest extends ATestCase
{
    /**
     * @param $command
     * @param $expected
     * @dataProvider dpDispatch
     */
    public function testDispatch($command, $expected)
    {
        $bus = new CommandBus($this->getSchema());
        $result = $bus->dispatch($command);

        $this->assertEquals($expected, $result);
    }

    public function dpDispatch()
    {
        $commandb = new CommandBusTestCommandB();
        $commandb->proprerties = [
            'first' => true,
            'last'  => true,
        ];

        $resultb = new OperationResult(true);
        $resultb->setData(['commandB' => true], 'result');
        $resultb->setData($commandb, 'command');

        return [
            [
                new CommandBusTestCommandA(),
                new OperationResult(false, ['commandA' => true])
            ],
            [

                new CommandBusTestCommandB(),
                $resultb
            ]
        ];
    }

    public function getSchema()
    {
        return [
            CommandBusTestCommandA::class => [
                'handler' => static function ($command) {
                    return new OperationResult(false, ['commandA' => true]);
                }
            ],
            CommandBusTestCommandB::class => [
                'middleware' => [
                    'first' => static function (CommandBusTestCommandB $command, $next) {
                        $command->proprerties['first'] = true;

                        /** @var OperationResult $result */
                        $result = $next($command);

                        $data = $result->getData();

                        $result->setData([]);
                        $result->setData($data, 'result');
                        $result->setData($command, 'command');

                        return $result;
                    },
                    'last' => static function (CommandBusTestCommandB $command, $next) {
                        $command->proprerties['last'] = true;

                        return $next($command);
                    }
                ],
                'handler' => static function ($command) {
                    return new OperationResult(true, ['commandB' => true]);
                }
            ],
        ];
    }
}

class CommandBusTestCommandA
{

}

class CommandBusTestCommandB
{
    public $proprerties = [];
}