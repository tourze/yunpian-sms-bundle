<?php

namespace YunpianSmsBundle\Service;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Yiisoft\Json\Json;
use YunpianSmsBundle\Request\RequestInterface;

#[WithMonologChannel(channel: 'yunpian_sms')]
class SmsApiClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function requestArray(RequestInterface $request): array
    {
        $startTime = microtime(true);
        if (!$this->isTestEnvironment()) {
            $this->logger->info('云片短信API请求开始', [
                'method' => $request->getRequestMethod(),
                'path' => $request->getRequestPath(),
                'options' => $this->sanitizeOptions($request->getRequestOptions() ?? []),
            ]);
        }

        try {
            $response = $this->request($request);
            $json = $response->getContent();
            $decoded = Json::decode($json);

            if (!is_array($decoded)) {
                throw new \InvalidArgumentException('API response is not an array');
            }

            $typedArray = $this->ensureStringKeyedArray($decoded);

            $duration = microtime(true) - $startTime;
            if (!$this->isTestEnvironment()) {
                $this->logger->info('云片短信API请求成功', [
                    'duration' => round($duration * 1000, 2) . 'ms',
                    'status_code' => $response->getStatusCode(),
                    'response_data' => $typedArray,
                ]);
            }

            return $typedArray;
        } catch (\Throwable $e) {
            $duration = microtime(true) - $startTime;
            if (!$this->isTestEnvironment()) {
                $this->logger->error('云片短信API请求失败', [
                    'duration' => round($duration * 1000, 2) . 'ms',
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            throw $e;
        }
    }

    public function request(RequestInterface $request): ResponseInterface
    {
        $startTime = microtime(true);

        try {
            $response = $this->httpClient->request(
                $request->getRequestMethod() ?? 'POST',
                $request->getRequestPath(),
                $request->getRequestOptions() ?? [],
            );

            $duration = microtime(true) - $startTime;
            if (!$this->isTestEnvironment()) {
                $this->logger->debug('HTTP请求完成', [
                    'duration' => round($duration * 1000, 2) . 'ms',
                    'status_code' => $response->getStatusCode(),
                ]);
            }

            return $response;
        } catch (\Throwable $e) {
            $duration = microtime(true) - $startTime;
            if (!$this->isTestEnvironment()) {
                $this->logger->error('HTTP请求失败', [
                    'duration' => round($duration * 1000, 2) . 'ms',
                    'exception' => $e->getMessage(),
                ]);
            }
            throw $e;
        }
    }

    /**
     * 清理敏感信息，避免在日志中记录API密钥等敏感数据
     *
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function sanitizeOptions(array $options): array
    {
        $sanitized = $options;

        // 移除敏感的API密钥信息
        if (isset($sanitized['body']) && is_string($sanitized['body'])) {
            // 如果是form data，移除apikey参数
            $sanitized['body'] = preg_replace('/apikey=[^&]*/', 'apikey=***', $sanitized['body']) ?? $sanitized['body'];
        }

        if (isset($sanitized['headers']) && is_array($sanitized['headers']) && isset($sanitized['headers']['Authorization'])) {
            $sanitized['headers']['Authorization'] = '***';
        }

        return $sanitized;
    }

    /**
     * 验证数组是否为 array<string, mixed> 类型
     *
     * @param mixed $value
     *
     * @return array<string, mixed>
     */
    private function ensureStringKeyedArray(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        // 检查所有的键是否都是字符串
        foreach (array_keys($value) as $key) {
            if (!is_string($key)) {
                // 如果有非字符串键，转换为字符串键数组
                $stringKeyedArray = [];
                foreach ($value as $k => $v) {
                    $stringKeyedArray[(string) $k] = $v;
                }

                return $stringKeyedArray;
            }
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    private function isTestEnvironment(): bool
    {
        return 'true' === getenv('DISABLE_LOGGING_IN_TESTS')
            || 'test' === getenv('APP_ENV')
            || defined('PHPUNIT_COMPOSER_INSTALL')
            || false !== getenv('SYMFONY_PHPUNIT_VERSION');
    }
}
