<?php

namespace YunpianSmsBundle\Request;

use YunpianSmsBundle\Entity\Account;

trait AccountAware
{
    private Account $account;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }
}
