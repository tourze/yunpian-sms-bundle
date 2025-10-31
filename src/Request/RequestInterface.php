<?php

namespace YunpianSmsBundle\Request;

interface RequestInterface
{
    public function getRequestMethod(): ?string;

    public function getRequestPath(): string;

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array;
}
