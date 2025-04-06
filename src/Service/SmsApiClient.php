<?php

namespace YunpianSmsBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Yiisoft\Json\Json;
use YunpianSmsBundle\Request\RequestInterface;

class SmsApiClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    )
    {
    }

    public function request(RequestInterface $request): array
    {
        $response = $this->httpClient->request(
            $request->getRequestMethod(),
            $request->getRequestPath(),
            $request->getRequestOptions(),
        );

        $json = $response->getContent();
        return Json::decode($json);
    }
}
