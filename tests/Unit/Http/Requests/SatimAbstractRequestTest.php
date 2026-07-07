<?php

declare(strict_types=1);

use LaravelSatim\Http\Requests\SatimAbstractRequest;
use LaravelSatim\Http\Responses\SatimRegisterResponse;
use Psr\Http\Message\ResponseInterface;

function fakeRequest(): SatimAbstractRequest
{
    return new readonly class () extends SatimAbstractRequest {
        /**
         * @return array<string, mixed>
         */
        public function payload(): array
        {
            return $this->clean([
                'a' => 'kept',
                'b' => null,
                'nested' => ['c' => null],
                'deep' => ['d' => 'kept', 'e' => null],
            ]);
        }

        public function toResponse(ResponseInterface $response): SatimRegisterResponse
        {
            return SatimRegisterResponse::fromPsr($response);
        }
    };
}

it('removes null values and the empty arrays they leave behind', function (): void {
    expect(fakeRequest()->payload())->toBe([
        'a' => 'kept',
        'deep' => ['d' => 'kept'],
    ]);
});

it('defaults rules to an empty array', function (): void {
    expect(fakeRequest()->rules())->toBe([]);
});
