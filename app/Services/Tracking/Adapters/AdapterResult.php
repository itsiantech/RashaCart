<?php

namespace App\Services\Tracking\Adapters;

class AdapterResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $data = [],
        public readonly ?int $httpStatus = null,
        public readonly ?string $error = null,
        public readonly bool $retryable = true
    ) {}

    /**
     * Create a successful result
     */
    public static function success(string $message, array $data = [], int $httpStatus = 200): self
    {
        return new self(true, $message, $data, $httpStatus);
    }

    /**
     * Create a failed result
     */
    public static function failure(string $message, ?string $error = null, ?int $httpStatus = null, bool $retryable = true): self
    {
        return new self(false, $message, [], $httpStatus, $error, $retryable);
    }

    /**
     * Check if error is retryable
     */
    public function isRetryable(): bool
    {
        return $this->retryable;
    }

    /**
     * Get the response data
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the error message
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Get HTTP status code
     */
    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'http_status' => $this->httpStatus,
            'error' => $this->error,
            'retryable' => $this->retryable,
        ];
    }
}
