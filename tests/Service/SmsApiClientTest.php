<?php

namespace YunpianSmsBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yiisoft\Json\Json;
use YunpianSmsBundle\Request\RequestInterface;
use YunpianSmsBundle\Service\SmsApiClient;

class SmsApiClientTest extends TestCase
{
    private SmsApiClient $client;
    private MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = new SmsApiClient($this->httpClient);
    }

    public function testRequest_withSuccessfulResponse(): void
    {
        // 准备测试数据
        $mockResponse = $this->createMock(ResponseInterface::class);
        $expectedData = ['code' => 0, 'msg' => 'OK', 'data' => ['test' => 'value']];
        $jsonResponse = Json::encode($expectedData);
        
        // 设置模拟对象预期行为
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockRequest->expects($this->once())
            ->method('getRequestMethod')
            ->willReturn('POST');
        $mockRequest->expects($this->once())
            ->method('getRequestPath')
            ->willReturn('/v2/sms/send');
        $mockRequest->expects($this->once())
            ->method('getRequestOptions')
            ->willReturn(['form_params' => ['mobile' => '13800138000', 'text' => 'Test message']]);
            
        $mockResponse->expects($this->once())
            ->method('getContent')
            ->willReturn($jsonResponse);
            
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/v2/sms/send',
                ['form_params' => ['mobile' => '13800138000', 'text' => 'Test message']]
            )
            ->willReturn($mockResponse);
        
        // 执行测试
        $result = $this->client->request($mockRequest);
        
        // 断言结果
        $this->assertSame($expectedData, $result);
    }
    
    public function testRequest_withErrorResponse(): void
    {
        // 准备测试数据
        $mockResponse = $this->createMock(ResponseInterface::class);
        $errorData = ['code' => 2, 'msg' => 'Error message', 'detail' => 'Detailed error'];
        $jsonResponse = Json::encode($errorData);
        
        // 设置模拟对象预期行为
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockRequest->expects($this->once())
            ->method('getRequestMethod')
            ->willReturn('POST');
        $mockRequest->expects($this->once())
            ->method('getRequestPath')
            ->willReturn('/v2/sms/send');
        $mockRequest->expects($this->once())
            ->method('getRequestOptions')
            ->willReturn(['form_params' => ['mobile' => '13800138000', 'text' => 'Test message']]);
            
        $mockResponse->expects($this->once())
            ->method('getContent')
            ->willReturn($jsonResponse);
            
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);
        
        // 执行测试
        $result = $this->client->request($mockRequest);
        
        // 断言结果
        $this->assertSame($errorData, $result);
    }

    public function testRequest_withJsonDecodingError(): void
    {
        // 准备测试数据
        $mockResponse = $this->createMock(ResponseInterface::class);
        $invalidJson = '{invalid:json}';
        
        // 设置模拟对象预期行为
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockRequest->method('getRequestMethod')->willReturn('GET');
        $mockRequest->method('getRequestPath')->willReturn('/test/path');
        $mockRequest->method('getRequestOptions')->willReturn([]);
            
        $mockResponse->method('getContent')->willReturn($invalidJson);
        $this->httpClient->method('request')->willReturn($mockResponse);
        
        // 预期会抛出异常
        $this->expectException(\JsonException::class);
        
        // 执行测试
        $this->client->request($mockRequest);
    }
} 