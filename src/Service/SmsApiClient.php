<?php

namespace YunpianSmsBundle\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yiisoft\Json\Json;
use YunpianSmsBundle\Request\RequestInterface;

class SmsApiClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    )
    {
    }

    public function requestArray(RequestInterface $request): array
    {
        $response = $this->request($request);

        $json = $response->getContent();
        return Json::decode($json);
    }

    public function request(RequestInterface $request): ResponseInterface
    {
        return $this->httpClient->request(
            $request->getRequestMethod(),
            $request->getRequestPath(),
            $request->getRequestOptions(),
        );
    }
}
