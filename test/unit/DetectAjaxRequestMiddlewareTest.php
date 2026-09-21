<?php

declare(strict_types=1);

namespace WebwareTest\Htmx;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Webware\Htmx\Http\Middleware\DetectAjaxRequestMiddleware;

#[CoversClass(DetectAjaxRequestMiddleware::class)]
final class DetectAjaxRequestMiddlewareTest extends TestCase
{
    #[Test]
    public function itDisablesLayoutForHtmxRequests(): void
    {
        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects(self::once())
            ->method('addDefaultParam')
            ->with(TemplateRendererInterface::TEMPLATE_ALL, 'layout', false);

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new EmptyResponse());

        $middleware = new DetectAjaxRequestMiddleware($template, []);
        $request    = new ServerRequest()->withAttribute('hx-request', true);

        $middleware->process($request, $handler);
    }

    #[Test]
    public function itLeavesLayoutUntouchedForNonHtmxRequests(): void
    {
        $template = $this->createMock(TemplateRendererInterface::class);
        $template->expects(self::never())->method('addDefaultParam');

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new EmptyResponse());

        $middleware = new DetectAjaxRequestMiddleware($template, []);
        $request    = new ServerRequest();

        $middleware->process($request, $handler);
    }

    #[Test]
    public function itReturnsTheHandlerResponse(): void
    {
        $template = $this->createStub(TemplateRendererInterface::class);
        $expected = new EmptyResponse();
        $handler  = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($expected);

        $middleware = new DetectAjaxRequestMiddleware($template, []);
        $request    = new ServerRequest()->withAttribute('hx-request', true);

        self::assertSame($expected, $middleware->process($request, $handler));
    }
}
