<?php
namespace Djunehor\Monnify;
use Djunehor\Monnify\Monnify;
use PHPUnit\Framework\TestCase;


class MonnifyAccountTest extends TestCase
{
    private $monnify;
    private $username = 'MK_TEST_WD7TZCMQV7';
    private $password = 'H5EQMQSHSURJNQ7UH2R78YAH6UN54ZP7';
    private $contractCode = '2957982769';
    public $reserveAccountPayload = [
        "accountReference" => '9900725554',
        "accountName" => "Test Reserved Account",
        "currencyCode" => "NGN",
        "contractCode" => "2957982769",
        "customerEmail" => "test@tester.com",
        "incomeSplitConfig" => [
            [
                "subAccountCode" => "MFY_SUB_319452883228",
                "feePercentage" => 10.5,
                "splitPercentage" => 20,
                "feeBearer" => true
            ]
        ]
        ];

    public function setUp(): void
    {
        $this->monnify = new Monnify($this->username, $this->password, $this->contractCode);
        $this->monnify->authenticate();
    }

    /**
     * Fails if API call fails
     */
    public function testReserveAccount() : void
    {
        $payload = $this->reserveAccountPayload;
        $expectedReference = $payload['accountReference'];

        $response = $this->monnify->reserveAccount($this->reserveAccountPayload);

        $this->assertTrue($response && $response->accountReference == $expectedReference);
    }

    /**
     * Fails if API encounter error
     */
    public function testGetTransactionStatus() : void
    {
        $expectedReference = $this->reserveAccountPayload['accountReference'];

        $response = $this->monnify->getTransactionStatus($expectedReference);
        $this->assertEquals($response->accountReference, $expectedReference);
    }


    public function testUnReserveNotExistAccount() : void
    {
        $accountNumber = '123456789';
        $response = $this->monnify->unReserveAccount($accountNumber);
        $this->assertFalse($response);

    }

    public function testUnReserveExistAccount() : void
    {
        $payload = $this->reserveAccountPayload;
        $reserveResponse = $this->monnify->reserveAccount($payload);
        $accountNumber = $reserveResponse->accountNumber;
        $unReserveResponse = $this->monnify->unReserveAccount($accountNumber);
        $this->assertNotFalse($unReserveResponse);

    }

}
