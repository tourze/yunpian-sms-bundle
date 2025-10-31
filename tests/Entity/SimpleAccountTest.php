<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YunpianSmsBundle\Entity\Account;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class SimpleAccountTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Account();
    }

    public function testEntity(): void
    {
        $entity = new Account();
        $this->assertInstanceOf(Account::class, $entity);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'apiKey' => ['apiKey', 'test-api-key'],
            'valid' => ['valid', true],
        ];
    }
}
