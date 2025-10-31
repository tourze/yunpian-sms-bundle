<?php

declare(strict_types=1);

namespace YunpianSmsBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use YunpianSmsBundle\YunpianSmsBundle;

/**
 * @internal
 */
#[CoversClass(YunpianSmsBundle::class)]
#[RunTestsInSeparateProcesses]
final class YunpianSmsBundleTest extends AbstractBundleTestCase
{
}
