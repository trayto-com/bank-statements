<?php

namespace JakubZapletal\Component\BankStatement\Tests\Parser;

use JakubZapletal\Component\BankStatement\Parser\ABO\CSOBCZParser;
use PHPUnit\Framework\TestCase;

class CSOBCZParserTest extends TestCase
{
    /**
     * @var string
     */
    protected $parserClassName = CSOBCZParser::class;

    public function testParseFileObject()
    {
        $parser = new CSOBCZParser();

        $reflectionParser = new \ReflectionClass($this->parserClassName);
        $method = $reflectionParser->getMethod('parseFileObject');
        $method->setAccessible(true);

        # Positive statement
        $fileObject = new \SplFileObject(tempnam(sys_get_temp_dir(), 'test_'), 'w+');
        $fileObject->fwrite(
            '0741234561234567890Test s.r.o.         01011400000000100000+00000000080000+00000000060000' .
            '+00000000040000+002010214              ' . PHP_EOL
        );
        $fileObject->fwrite(
            '0750000000000012345000000000015678900000000020010000000400002000000001100100000120000000013050114' .
            'Tran 1              00203050114' . PHP_EOL
        );
        $fileObject->fwrite(
            '07600000000000000000000002001050114Protistrana s.r.o.' . PHP_EOL
        );
        $fileObject->fwrite(
            '078First line' . PHP_EOL
        );
        $fileObject->fwrite(
            '079Second line' . PHP_EOL
        );
        $statement = $method->invokeArgs($parser, array($fileObject));

        # Transaction currency
        $statement->rewind();
        $transaction = $statement->current();
        $this->assertEquals('CZK', $transaction->getCurrency());
    }
}
