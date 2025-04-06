<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Repository\SignRepository;
use YunpianSmsBundle\Request\Sign\AddSignRequest;
use YunpianSmsBundle\Request\Sign\DeleteSignRequest;
use YunpianSmsBundle\Request\Sign\GetSignListRequest;
use YunpianSmsBundle\Request\Sign\UpdateSignRequest;

class SignService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SignRepository $signRepository,
        private readonly SmsApiClient $apiClient,
    ) {
    }

    public function syncSigns(Account $account): void
    {
        // 获取远程签名列表
        $request = new GetSignListRequest();
        $request->setAccount($account);
        $response = $this->apiClient->request($request);

        foreach ($response as $signData) {
            $sign = $this->signRepository->findOneByAccountAndSign($account, $signData['sign']);

            if (!$sign) {
                $sign = new Sign();
                $sign->setAccount($account);
                $sign->setSign($signData['sign']);
            }

            $sign->setApplyState($signData['apply_state']);
            $sign->setWebsite($signData['website'] ?? null);
            $sign->setNotify($signData['notify'] ?? true);
            $sign->setApplyVip($signData['apply_vip'] ?? false);
            $sign->setIsOnlyGlobal($signData['is_only_global'] ?? false);
            $sign->setIndustryType($signData['industry_type'] ?? '其它');
            $sign->setProveType($signData['prove_type'] ?? null);
            $sign->setLicenseUrls($signData['license_urls'] ?? null);
            $sign->setIdCardName($signData['id_card_name'] ?? null);
            $sign->setIdCardNumber($signData['id_card_number'] ?? null);
            $sign->setIdCardFront($signData['id_card_front'] ?? null);
            $sign->setIdCardBack($signData['id_card_back'] ?? null);
            $sign->setSignUse($signData['sign_use'] ?? 0);

            $this->entityManager->persist($sign);
        }

        $this->entityManager->flush();
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

    public function updateSign(Sign $sign): void
    {
        $request = new UpdateSignRequest();
        $request->setAccount($sign->getAccount());
        $request->setOldSign($sign->getSign());
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
}
