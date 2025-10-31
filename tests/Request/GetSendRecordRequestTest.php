<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\GetSendRecordRequest;

/**
 * @internal
 */
#[CoversClass(GetSendRecordRequest::class)]
final class GetSendRecordRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetSendRecordRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/sms/get_record.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetSendRecordRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetSendRecordRequest();

        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);

        $startTime = new \DateTime('2023-05-01');
        $endTime = new \DateTime('2023-05-02');
        $mobile = '13800138000';

        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setMobile($mobile);

        /** @var array<string, mixed> $options */
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('body', $options);
        $this->assertArrayHasKey('headers', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertIsString($body);

        // 验证body包含必要的参数
        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('start_time=2023-05-01+00%3A00%3A00', $body);
        $this->assertStringContainsString('end_time=2023-05-02+00%3A00%3A00', $body);
        $this->assertStringContainsString('mobile=13800138000', $body);

        // 验证Content-Type header
        /** @var array<string, string> $headers */
        $headers = $options['headers'];
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/x-www-form-urlencoded', $headers['Content-Type']);
    }

    public function testGetters(): void
    {
        $request = new GetSendRecordRequest();

        $startTime = new \DateTime('2023-05-01');
        $endTime = new \DateTime('2023-05-02');
        $mobile = '13800138000';

        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setMobile($mobile);

        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($endTime, $request->getEndTime());
        $this->assertEquals($mobile, $request->getMobile());
    }
}
