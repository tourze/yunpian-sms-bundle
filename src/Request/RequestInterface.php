<?php

namespace YunpianSmsBundle\Request;

interface RequestInterface
{
    public function getRequestMethod(): ?string;

    public function getRequestPath(): string;

    public function getRequestOptions(): ?array;
}
