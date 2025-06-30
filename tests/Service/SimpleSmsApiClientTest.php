<?php

namespace YunpianSmsBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use YunpianSmsBundle\Request\RequestInterface;
use YunpianSmsBundle\Service\SmsApiClient;

class SimpleSmsApiClientTest extends TestCase
{
    public function testRequest(): void
    {
        // 创建模拟对象
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(RequestInterface::class);
        
        // 设置模拟对象的行为
        $request->method('getRequestMethod')->willReturn('POST');
        $request->method('getRequestPath')->willReturn('/test/path');
        $request->method('getRequestOptions')->willReturn(['form_params' => ['test' => 'value']]);
        
        $response->method('getContent')->willReturn('{"code":0,"msg":"success"}');
        
        $httpClient->method('request')
            ->with('POST', '/test/path', ['form_params' => ['test' => 'value']])
            ->willReturn($response);
        
        // 创建被测类实例
        $client = new SmsApiClient($httpClient);
        
        // 执行测试
        $result = $client->requestArray($request);
        
        // 验证结果
        $this->assertEquals(['code' => 0, 'msg' => 'success'], $result);
    }
} 