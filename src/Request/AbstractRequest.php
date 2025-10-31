<?php

namespace YunpianSmsBundle\Request;

abstract class AbstractRequest implements RequestInterface
{
    abstract public function getMethod(): string;

    abstract public function getUri(): string;

    /**
     * @return array<string, string>
     */
    abstract public function getHeaders(): array;

    /**
     * @return array<string, mixed>
     */
    abstract public function getBody(): array;

    public function getRequestMethod(): ?string
    {
        return $this->getMethod();
    }

    public function getRequestPath(): string
    {
        return $this->getUri();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $body = $this->getBody();

        // 使用 Symfony HttpClient 兼容的参数
        // 对于 form data，需要手动构建查询字符串
        return [
            'body' => http_build_query($body),
            'headers' => array_merge($this->getHeaders(), [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]),
        ];
    }
}
