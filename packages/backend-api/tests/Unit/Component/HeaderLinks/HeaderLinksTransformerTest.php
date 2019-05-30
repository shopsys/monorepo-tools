<?php

declare(strict_types=1);

namespace Tests\BackendApiBundle\Unit\Component\HeaderLinks;

use PHPUnit\Framework\TestCase;
use Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinks;
use Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinksTransformer;
use Shopsys\FrameworkBundle\Component\Paginator\PaginationResult;

/**
 * @experimental
 */
class HeaderLinksTransformerTest extends TestCase
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult $paginationResult
     * @param \Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinks $expectedHeaderLinks
     * @dataProvider getFromPaginationResultTestCases
     */
    public function testFromPaginationResult(PaginationResult $paginationResult, HeaderLinks $expectedHeaderLinks)
    {
        $transformer = new HeaderLinksTransformer();
        $headerLinks = $transformer->fromPaginationResult($paginationResult, 'http://example.com/x');
        $this->assertEquals($expectedHeaderLinks, $headerLinks);
    }

    public function getFromPaginationResultTestCases()
    {
        yield [
            new PaginationResult(1, 20, 10, []),
            new HeaderLinks(),
        ];
        yield [
            new PaginationResult(1, 10, 10, []),
            new HeaderLinks(),
        ];
        yield [
            new PaginationResult(1, 10, 20, []),
            $this->createHeaderLinks(null, null, '2', '2'),
        ];
        yield [
            new PaginationResult(2, 10, 20, []),
            $this->createHeaderLinks('1', '1'),
        ];
        yield [
            new PaginationResult(3, 10, 100, []),
            $this->createHeaderLinks('1', '2', '4', '10'),
        ];
    }

    /**
     * @param string|null $first
     * @param string|null $prev
     * @param string|null $next
     * @param string|null $last
     * @return \Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinks
     */
    protected function createHeaderLinks(?string $first = null, ?string $prev = null, ?string $next = null, ?string  $last = null): HeaderLinks
    {
        $headerLinks = new HeaderLinks();
        if ($first) {
            $headerLinks = $headerLinks->add('http://example.com/x?page=' . $first, 'first');
        }
        if ($prev) {
            $headerLinks = $headerLinks->add('http://example.com/x?page=' . $prev, 'prev');
        }
        if ($next) {
            $headerLinks = $headerLinks->add('http://example.com/x?page=' . $next, 'next');
        }
        if ($last) {
            $headerLinks = $headerLinks->add('http://example.com/x?page=' . $last, 'last');
        }

        return $headerLinks;
    }
}
