<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;

class JsConfig implements ArgumentInterface
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Registry
     */
    private $variableRegistry;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param SerializerInterface $serializer
     * @param UrlInterface        $urlBuilder
     * @param Registry            $variableRegistry
     * @param Repository          $assetRepo
     * @param RequestInterface    $request
     * @param Config              $config
     */
    public function __construct(
        SerializerInterface $serializer,
        UrlInterface $urlBuilder,
        Registry $variableRegistry,
        Repository $assetRepo,
        RequestInterface $request,
        Config  $config
    ) {
        $this->serializer = $serializer;
        $this->urlBuilder = $urlBuilder;
        $this->variableRegistry = $variableRegistry;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Get serialized params for product filter js.
     *
     * @return string
     */
    public function getJson(): string
    {
        return $this->serializer->serialize($this->get());
    }

    /**
     * Get params for product filter js.
     *
     * @return array
     */
    public function get(): array
    {
        $variables = $this->variableRegistry->get();
        return [
            'auto' => true,
            'cleanUrl' => $this->getCurrentUrl($variables),
            'variables' => $this->convertValuesToStrings($variables),
            'loader' => [
                'icon' => $this->getViewFileUrl('images/loader-1.gif'),
            ],
            'scrollUp' => $this->config->shouldScrollUpAfterUpdate(),
            'scrollUpTo' => $this->config->getScrollUpSelector(),
        ];
    }

    /**
     * Retrieve formatted current url
     *
     * @param  array $variables
     * @return string
     */
    public function getCurrentUrl(array $variables = []): string
    {
        $params = ['_current' => true, '_use_rewrite' => true, 'p' => null];
        $params += array_fill_keys(array_keys($variables), null);
        $url = $this->urlBuilder->getUrl('*/*/*', $params);
        return str_replace('catalogsearch/result/index', 'catalogsearch/result', $url);
    }

    /**
     * Convert all values to string to simplify comparison in js.
     *
     * @param  array $variables
     * @return array
     */
    private function convertValuesToStrings(array $variables): array
    {
        $variables = array_map(
            static function ($values) {
                return array_map('strval', $values);
            },
            $variables
        );

        if (isset($variables['price'])) {
            $variables['price'] = array_map(
                static function ($value) {
                    return str_replace('-', '_', $value);
                },
                $variables['price']
            );
        }

        return $variables;
    }

    /**
     * Get static file url.
     *
     * @param  string $fileId
     * @return string
     */
    private function getViewFileUrl(string $fileId): string
    {
        try {
            return $this->assetRepo->getUrlWithParams($fileId, ['_secure' => $this->request->isSecure()]);
        } catch (LocalizedException $e) {
            return '';
        }
    }
}
