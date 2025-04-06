<?php

namespace YunpianSmsBundle\Request;

abstract class AbstractRequest implements RequestInterface
{
    abstract public function getMethod(): string;
    abstract public function getUri(): string;
    abstract public function getHeaders(): array;
    abstract public function getBody(): array;

    public function getRequestMethod(): ?string
    {
        return $this->getMethod();
    }

    public function getRequestPath(): string
    {
        return $this->getUri();
    }

    public function getRequestOptions(): ?array
    {
        return [
            'headers' => $this->getHeaders(),
            'form_params' => $this->getBody(),
        ];
    }
}
