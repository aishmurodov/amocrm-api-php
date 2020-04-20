<?php

namespace AmoCRM\Client;

use AmoCRM\EntitiesServices\Account;
use AmoCRM\EntitiesServices\CatalogElements;
use AmoCRM\EntitiesServices\Catalogs;
use AmoCRM\EntitiesServices\Companies;
use AmoCRM\EntitiesServices\Contacts;
use AmoCRM\EntitiesServices\CustomFieldGroups;
use AmoCRM\EntitiesServices\CustomFields;
use AmoCRM\EntitiesServices\EntityTags;
use AmoCRM\EntitiesServices\Leads;
use AmoCRM\EntitiesServices\Roles;
use AmoCRM\EntitiesServices\Segments;
use AmoCRM\OAuth\AmoCRMOAuth;
use Exception;
use League\OAuth2\Client\Token\AccessToken;

class AmoCRMApiClient
{
    public const API_VERSION = 4;

    protected const HOST_PATTERN = '%s.amocrm.%s';

    /**
     * @var AmoCRMOAuth
     */
    protected $oAuthClient;
    /**
     * @var string
     */
    protected $accountBaseDomain;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var callable
     */
    private $accessTokenRefreshCallback;

    /**
     * AmoCRMApiClient constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     */
    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
    {
        $this->oAuthClient = new AmoCRMOAuth($clientId, $clientSecret, $redirectUri);
    }

    /**
     * Устанавливаем Access Token, который будет использован при запросах
     * @param AccessToken $accessToken
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Устанавливаем базовый домен аккаунта в amoCRM, который будет использован при запросах
     * @param string $domain
     * @return $this
     */
    public function setAccountBaseDomain(string $domain): self
    {
        $this->oAuthClient->setBaseDomain($domain);
        $this->accountBaseDomain = $domain;

        return $this;
    }

    /**
     * Получить базовый домен аккаунта в amoCRM, который будет использован при запросах
     * @return $this
     */
    public function getAccountBaseDomain(): string
    {
        return $this->accountBaseDomain;
    }

    /**
     * Устанавливаем callback, который будет вызван при обновлении AccessToken'а библиотеки
     * @param callable $callback
     * @return $this
     */
    public function onAccessTokenRefresh(callable $callback): self
    {
        $this->accessTokenRefreshCallback = $callback;

        return $this;
    }

    /**
     * Метод строит объект для совершения запросов для сервисов сущностей
     * @return AmoCRMApiRequest
     */
    private function buildRequest(): AmoCRMApiRequest
    {
        $oAuthClient = $this->getOAuthClient();
        $oAuthClient->setAccessTokenRefreshCallback($this->accessTokenRefreshCallback);

        $request = new AmoCRMApiRequest($this->accessToken, $oAuthClient);

        return $request;
    }

    /**
     * Метод вернет oAuthClient
     * @return AmoCRMOAuth
     */
    public function getOAuthClient(): AmoCRMOAuth
    {
        return $this->oAuthClient;
    }

    /**
     * Метод вернет объект тегов
     * @TODO make static
     * @param string $entityType
     * @return EntityTags
     * @throws Exception
     */
    public function tags(string $entityType)
    {
        $request = $this->buildRequest();

        $service = new EntityTags($request);

        if (!is_null($entityType)) {
            $service->setEntityType($entityType);
        }

        return $service;
    }

    /**
     * Метод вернет объект сделок
     * @TODO make static
     * @return Leads
     */
    public function leads()
    {
        $request = $this->buildRequest();

        return new Leads($request);
    }

    /**
     * Метод вернет объект контактов
     * @TODO make static
     * @return Contacts
     */
    public function contacts()
    {
        $request = $this->buildRequest();

        return new Contacts($request);
    }

    /**
     * Метод вернет объект компаний
     * @TODO make static
     * @return Companies
     */
    public function companies()
    {
        $request = $this->buildRequest();

        return new Companies($request);
    }

    /**
     * Метод вернет объект каталогов
     * @TODO make static
     * @return Catalogs
     */
    public function catalogs()
    {
        $request = $this->buildRequest();

        return new Catalogs($request);
    }

    /**
     * Метод вернет объект элементов каталогов
     * @TODO make static
     * @return CatalogElements
     */
    public function catalogElements()
    {
        $request = $this->buildRequest();

        return new CatalogElements($request);
    }

    /**
     * Метод вернет объект кастом полей
     * @TODO make static
     * @param string $entityType
     * @return CustomFields
     * @throws Exception
     */
    public function customFields(string $entityType)
    {
        $request = $this->buildRequest();

        $service = new CustomFields($request);

        if (!is_null($entityType)) {
            $service->setEntityType($entityType);
        }

        return $service;
    }

    /**
     * Метод вернет объект групп кастом полей (табы в карточке)
     * @TODO make static
     * @param string|null $entityType
     * @return CustomFieldGroups
     * @throws Exception
     */
    public function customFieldGroups(string $entityType = null)
    {
        $request = $this->buildRequest();

        $service = new CustomFieldGroups($request);

        if (!is_null($entityType)) {
            $service->setEntityType($entityType);
        }

        return $service;
    }

    /**
     * Метод вернет объект аккаунта
     * @TODO make static
     * @return Account
     */
    public function account()
    {
        $request = $this->buildRequest();

        return new Account($request);
    }

    /**
     * Метод вернет объект ролей пользователей
     * @TODO make static
     * @return Roles
     */
    public function roles()
    {
        $request = $this->buildRequest();

        return new Roles($request);
    }


    /**
     * Метод вернет объект сегментов покупателей
     * @TODO make static
     * @return Segments
     */
    public function customersSegments()
    {
        $request = $this->buildRequest();

        return new Segments($request);
    }
}
