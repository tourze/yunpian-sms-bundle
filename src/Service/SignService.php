<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Repository\SignRepository;
use YunpianSmsBundle\Request\Sign\AddSignRequest;
use YunpianSmsBundle\Request\Sign\DeleteSignRequest;
use YunpianSmsBundle\Request\Sign\GetSignRequest;
use YunpianSmsBundle\Request\Sign\UpdateSignRequest;

#[WithMonologChannel(channel: 'yunpian_sms')]
class SignService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SignRepository $signRepository,
        private readonly SmsApiClient $apiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 同步签名数据
     *
     * @return Sign[]
     */
    public function syncSigns(Account $account): array
    {
        try {
            // 获取远程签名列表
            $request = new GetSignRequest();
            $request->setAccount($account);
            $response = $this->apiClient->requestArray($request);

            $result = [];

            foreach ($response as $signData) {
                if (!is_array($signData)) {
                    continue;
                }

                $signText = $signData['sign'] ?? null;
                if (!is_string($signText)) {
                    continue;
                }

                $sign = $this->signRepository->findOneBy([
                    'account' => $account,
                    'sign' => $signText,
                ]);

                if (null === $sign) {
                    $sign = new Sign();
                    $sign->setAccount($account);
                    $sign->setSign($signText);
                    $this->entityManager->persist($sign);
                }

                $applyState = $signData['apply_state'] ?? null;
                $enabled = $signData['enabled'] ?? false;
                $website = $signData['website'] ?? null;

                $sign->setApplyState(is_string($applyState) ? $applyState : '');
                $sign->setValid(is_bool($enabled) ? $enabled : (bool) $enabled);

                // 更新其他属性
                if (is_string($website)) {
                    $sign->setWebsite($website);
                }

                $result[] = $sign;
            }

            $this->entityManager->flush();

            return $result;
        } catch (\Throwable $e) {
            $this->logger->error('同步签名失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return [];
        }
    }

    public function createSign(Sign $sign): void
    {
        $request = new AddSignRequest();
        $request->setAccount($sign->getAccount());
        $request->setSign($sign->getSign());
        $request->setNotify($sign->isNotify());
        $request->setApplyVip($sign->isApplyVip());
        $request->setIsOnlyGlobal($sign->isOnlyGlobal());
        $request->setIndustryType($sign->getIndustryType());
        $request->setProveType($sign->getProveType());
        $request->setLicenseUrls($sign->getLicenseUrls());
        $request->setIdCardName($sign->getIdCardName());
        $request->setIdCardNumber($sign->getIdCardNumber());
        $request->setIdCardFront($sign->getIdCardFront());
        $request->setIdCardBack($sign->getIdCardBack());
        $request->setSignUse($sign->getSignUse());

        $response = $this->apiClient->requestArray($request);
        $applyState = $response['apply_state'] ?? 'PENDING';
        $sign->setApplyState(is_string($applyState) ? $applyState : 'PENDING');
    }

    /**
     * 创建签名
     */
    public function create(Account $account, string $signContent, ?string $remark = null): Sign
    {
        try {
            $request = new AddSignRequest();
            $request->setAccount($account);
            $request->setSign($signContent);

            $response = $this->apiClient->requestArray($request);

            $sign = new Sign();
            $sign->setAccount($account);
            $sign->setSign($signContent);
            $sign->setApplyState('SUCCESS');

            $signId = $response['sign_id'] ?? null;
            if (is_string($signId) || is_int($signId)) {
                $sign->setSignId(is_int($signId) ? $signId : (int) $signId);
            }

            if (null !== $remark) {
                $sign->setRemark($remark);
            }

            $this->entityManager->persist($sign);
            $this->entityManager->flush();

            return $sign;
        } catch (\Throwable $e) {
            $this->logger->error('创建签名失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * 更新签名
     */
    public function update(Sign $sign, string $newSignContent): Sign
    {
        try {
            $request = new UpdateSignRequest();
            $request->setAccount($sign->getAccount());
            $request->setSign($newSignContent);

            // 使用signId作为标识符（如果可用），否则使用当前签名内容作为oldSign
            $signId = $sign->getSignId();
            if (null !== $signId) {
                $request->setSignId($signId);
            } else {
                $request->setOldSign($sign->getSign());
            }

            $this->apiClient->requestArray($request);

            $sign->setSign($newSignContent);
            $this->entityManager->flush();

            return $sign;
        } catch (\Throwable $e) {
            $this->logger->error('更新签名失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * 删除签名
     */
    public function delete(Sign $sign): bool
    {
        try {
            $request = new DeleteSignRequest();
            $request->setAccount($sign->getAccount());
            $request->setSign($sign->getSign());

            $response = $this->apiClient->requestArray($request);

            // 检查API响应状态
            $status = $response['status'] ?? null;
            if (is_string($status) && 'SUCCESS' === $status) {
                // 只有已持久化的实体才需要删除
                if ($sign->getId() > 0) {
                    $this->entityManager->remove($sign);
                    $this->entityManager->flush();
                }

                return true;
            }

            return false;
        } catch (\Throwable $e) {
            $this->logger->error('删除签名失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * @return Sign[]
     */
    public function findByAccount(Account $account): array
    {
        return $this->signRepository->findByAccount($account);
    }

    public function findOneByAccountAndSign(Account $account, string $sign): ?Sign
    {
        return $this->signRepository->findOneByAccountAndSign($account, $sign);
    }

    public function updateSign(Sign $sign): void
    {
        $request = new UpdateSignRequest();
        $request->setAccount($sign->getAccount());
        $request->setSign($sign->getSign());

        // 使用signId作为标识符（如果可用），否则使用当前签名内容作为oldSign
        $signId = $sign->getSignId();
        if (null !== $signId) {
            $request->setSignId($signId);
        } else {
            $request->setOldSign($sign->getSign());
        }

        $response = $this->apiClient->requestArray($request);
        $applyState = $response['apply_state'] ?? 'PENDING';
        $sign->setApplyState(is_string($applyState) ? $applyState : 'PENDING');

        $this->entityManager->flush();
    }

    public function deleteSign(Sign $sign): void
    {
        $request = new DeleteSignRequest();
        $request->setAccount($sign->getAccount());
        $request->setSign($sign->getSign());

        $this->apiClient->requestArray($request);
        $this->entityManager->remove($sign);
        $this->entityManager->flush();
    }
}
