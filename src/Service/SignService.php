<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Repository\SignRepository;
use YunpianSmsBundle\Request\Sign\AddSignRequest;
use YunpianSmsBundle\Request\Sign\DeleteSignRequest;
use YunpianSmsBundle\Request\Sign\GetSignRequest;
use YunpianSmsBundle\Request\Sign\UpdateSignRequest;

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
            $response = $this->apiClient->request($request);
            
            $result = [];
            
            foreach ($response as $signData) {
                $sign = $this->signRepository->findOneBy([
                    'account' => $account,
                    'sign' => $signData['sign'],
                ]);
    
                if ($sign === null) {
                    $sign = new Sign();
                    $sign->setAccount($account);
                    $sign->setSign($signData['sign']);
                    $this->entityManager->persist($sign);
                }
    
                $sign->setApplyState($signData['apply_state']);
                $sign->setValid($signData['enabled'] ?? false);
                
                // 更新其他属性
                if (isset($signData['website'])) {
                    $sign->setWebsite($signData['website']);
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

        $response = $this->apiClient->request($request);
        $sign->setApplyState($response['apply_state']);

        $this->entityManager->persist($sign);
        $this->entityManager->flush();
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
            
            $response = $this->apiClient->request($request);
            
            $sign = new Sign();
            $sign->setAccount($account);
            $sign->setSign($signContent);
            $sign->setApplyState('SUCCESS');
            
            if (isset($response['sign_id'])) {
                $sign->setSignId($response['sign_id']);
            }
            
            if ($remark !== null) {
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
            
            $this->apiClient->request($request);
            
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
    
            $this->apiClient->request($request);
            $this->entityManager->remove($sign);
            $this->entityManager->flush();
            
            return true;
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
        
        $response = $this->apiClient->request($request);
        $sign->setApplyState($response['apply_state'] ?? 'PENDING');
        
        $this->entityManager->flush();
    }

    public function deleteSign(Sign $sign): void
    {
        $request = new DeleteSignRequest();
        $request->setAccount($sign->getAccount());
        $request->setSign($sign->getSign());
        
        $this->apiClient->request($request);
        $this->entityManager->remove($sign);
        $this->entityManager->flush();
    }
}
