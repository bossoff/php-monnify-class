<?php
namespace Djunehor\Monnify;
use Djunehor\Monnify\Monnify;
use PHPUnit\Framework\TestCase;


class MonnifyTest extends TestCase
{
    private $username = 'MK_TEST_WD7TZCMQV7';
    private $password = 'H5EQMQSHSURJNQ7UH2R78YAH6UN54ZP7';
    private $contractCode = '2957982769';

    public function testAuthenticate(): void
    {
        $monnify = new Monnify($this->username, $this->password, $this->contractCode);
        $this->assertNotFalse($monnify->authenticate());
    }

    public function testGetAccessToken() : void
    {
        $monnify = new Monnify($this->username, $this->password, $this->contractCode);
        $monnify->authenticate();
        $this->assertNotNull($monnify->getAccessToken());
    }


    public function testIsValidToken(): void {
        $monnify = new Monnify($this->username, $this->password, $this->contractCode);
        $monnify->authenticate();

        sleep(5);

        $this->assertTrue($monnify->isTokenValid());
    }
}
