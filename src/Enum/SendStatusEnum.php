<?php

namespace YunpianSmsBundle\Enum;

enum SendStatusEnum: string
{
    case PENDING = 'pending';
    case SENDING = 'sending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case DELIVERED = 'delivered';
    case UNDELIVERED = 'undelivered';
}
