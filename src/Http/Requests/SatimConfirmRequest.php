<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Validation\Rule;
use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Enums\Language;
use LaravelSatim\Exceptions\SatimResponseException;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class SatimConfirmRequest extends SatimAbstractRequest
{
    public function __construct(
        public string $mdOrder,
        public ?Language $language = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'mdOrder' => $this->mdOrder,
            'language' => Language::withFallback($this->language)->code(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'mdOrder' => ['required', 'string', 'size:20'],
            'language' => ['nullable', Rule::enum(Language::class)],
        ];
    }

    /**
     * @throws SatimResponseException
     */
    public function toResponse(ResponseInterface $response): SatimResponseInterface
    {
        return SatimConfirmResponse::fromPsr($response);
    }
}
