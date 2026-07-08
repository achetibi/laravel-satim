<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimHttpClientInterface;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Contracts\SatimValidatorInterface;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Http\Requests\SatimStatusRequest;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Http\Responses\SatimRefundResponse;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use LaravelSatim\Http\Responses\SatimStatusResponse;
use LaravelSatim\SatimGateway;
use Psr\Http\Message\ResponseInterface;

function recordingValidator(): SatimValidatorInterface
{
    return new class () implements SatimValidatorInterface {
        public bool $validated = false;

        public function validate(SatimRequestInterface $request): void
        {
            $this->validated = true;
        }
    };
}

function fakeGatewayClient(ResponseInterface $response): SatimHttpClientInterface
{
    return new class ($response) implements SatimHttpClientInterface {
        /** @var list<string> */
        public array $endpoints = [];

        public function __construct(private readonly ResponseInterface $response)
        {
        }

        public function send(string $endpoint, SatimRequestInterface $request): ResponseInterface
        {
            $this->endpoints[] = $endpoint;

            return $this->response;
        }
    };
}

it('validates then registers against the register endpoint', function (): void {
    $validator = recordingValidator();
    $client = fakeGatewayClient(jsonResponse(['errorCode' => '0', 'formUrl' => 'https://pay.test']));
    $gateway = new SatimGateway($client, $validator);

    $response = $gateway->register(new SatimRegisterRequest(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://shop.test/return',
        udf1: 'U1',
    ));

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($validator->validated)->toBeTrue()
        ->and($client->endpoints)->toBe(['/register.do']);
});

it('confirms against the acknowledge endpoint', function (): void {
    $client = fakeGatewayClient(jsonResponse(['errorCode' => '0', 'OrderStatus' => 2]));
    $gateway = new SatimGateway($client, recordingValidator());

    $response = $gateway->confirm(new SatimConfirmRequest(mdOrder: 'BnTjnFDzZSP97QXu8FXq'));

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($client->endpoints)->toBe(['/public/acknowledgeTransaction.do']);
});

it('refunds against the refund endpoint', function (): void {
    $client = fakeGatewayClient(jsonResponse(['errorCode' => '0']));
    $gateway = new SatimGateway($client, recordingValidator());

    $response = $gateway->refund(new SatimRefundRequest(orderId: 'ORD123', amount: 50.00));

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($client->endpoints)->toBe(['/refund.do']);
});

it('fetches the status against the extended order status endpoint', function (): void {
    $client = fakeGatewayClient(jsonResponse(['errorCode' => '0', 'orderStatus' => 2]));
    $gateway = new SatimGateway($client, recordingValidator());

    $response = $gateway->status(new SatimStatusRequest(orderId: 'ehf9z2yvvThwQ4AACW2G'));

    expect($response)->toBeInstanceOf(SatimStatusResponse::class)
        ->and($client->endpoints)->toBe(['/getOrderStatusExtended.do']);
});
