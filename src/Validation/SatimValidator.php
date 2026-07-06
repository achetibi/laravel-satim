<?php

declare(strict_types=1);

namespace LaravelSatim\Validation;

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Contracts\SatimValidatorInterface;
use LaravelSatim\Exceptions\SatimValidationException;
use ReflectionObject;
use ReflectionProperty;

final class SatimValidator implements SatimValidatorInterface
{
    private array $merged = [];
    private array $excluded = [];

    public function __construct(
        private readonly ValidatorFactory $factory
    ) {
    }

    public function merge(array $rules): self
    {
        $this->merged = array_merge_recursive($this->merged, $rules);

        return $this;
    }

    public function exclude(string ...$fields): self
    {
        $this->excluded = array_merge($this->excluded, $fields);

        return $this;
    }

    /**
     * @throws SatimValidationException
     */
    public function validate(SatimRequestInterface $request): void
    {
        $rules = array_merge_recursive($request->rules(), $this->merged);
        $rules = array_diff_key($rules, array_flip($this->excluded));

        $validator = $this->factory->make(
            data: $this->data($request),
            rules: $rules,
            messages: trans('satim::validation'),
        );

        if ($validator->fails()) {
            throw SatimValidationException::withErrors($validator->errors());
        }
    }

    private function data(SatimRequestInterface $request): array
    {
        $data = [];

        foreach ((new ReflectionObject($request))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isInitialized($request)) {
                $data[$property->getName()] = $property->getValue($request);
            }
        }

        return $data;
    }
}
