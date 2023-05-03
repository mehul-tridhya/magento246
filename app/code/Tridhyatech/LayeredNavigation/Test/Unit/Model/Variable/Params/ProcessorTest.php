<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Test\Unit\Model\Variable\Params;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use PHPUnit\Framework\TestCase;
use Tridhyatech\LayeredNavigation\Model\Variable\Params\Processor;

class ProcessorTest extends TestCase
{
    /**
     * @var Processor
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = new Processor();
    }

    /**
     * @dataProvider getRequests()
     * @param        string $requestUri
     * @param        array  $variables
     * @param        string $expectedRequestUri
     * @param        array  $expectedParams
     */
    public function testMoveToParams(
        string $requestUri,
        array $variables,
        string $expectedRequestUri,
        array $expectedParams
    ): void {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $requestMock->setRequestUri($requestUri);

        $this->model->moveToParams($requestMock, $variables);
        foreach ($expectedParams as $expectedParam => $value) {
            self::assertSame($value, $requestMock->getParam($expectedParam));
        }
        self::assertSame($expectedRequestUri, $requestMock->getRequestUri());
    }

    public function getRequests(): \Generator
    {
        yield [
            'requestUri' => '/women/tops-women/jackets-women.html' .
                '?' . urlencode('prfilter_variables[color]') . '=' . urlencode('52,57') .
                '&' . urlencode('prfilter_variables[eco_collection]') . '=' . urlencode('0') .
                '&prfilter_ajax=1',
            'variables' => ['color' => ['52', '57'], 'eco_collection' => ['0']],
            'expectedRequestUri' => '/women/tops-women/jackets-women.html',
            'expectedParams' => ['color' => '52,57', 'eco_collection' => '0'],
        ];
        yield [
            'requestUri' => '/women/tops-women/jackets-women.html' .
                '?a=1' .
                '&' . urlencode('prfilter_variables[color]') . '=' . urlencode('52,57') .
                '&' . urlencode('prfilter_variables[eco_collection]') . '=' . urlencode('0') .
                '&prfilter_ajax=1',
            'variables' => ['color' => ['52', '57'], 'eco_collection' => ['0']],
            'expectedRequestUri' => '/women/tops-women/jackets-women.html?a=1',
            'expectedParams' => ['color' => '52,57', 'eco_collection' => '0'],
        ];
        yield [
            'requestUri' => '/women/tops-women/jackets-women.html' .
                '?a=1' .
                '&' . urlencode('prfilter_variables[color]') . '=' . urlencode('52,57') .
                '&' . urlencode('prfilter_variables[eco_collection]') . '=' . urlencode('0') .
                '&prfilter_ajax=1',
            'variables' => ['color' => ['52', '57']],
            'expectedRequestUri' => '/women/tops-women/jackets-women.html?a=1',
            'expectedParams' => ['color' => '52,57'],
        ];
    }
}
