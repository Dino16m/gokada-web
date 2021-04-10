<?php
namespace App\Traits;

use Psr\Http\Message\ResponseInterface;
use App\Exceptions\TransactionException;

trait EnsureOkResponse
{
    /**
     * @throws TransactionException
     */
    public function ensureOkResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() != 200) {
            $this->log((string) $response->getBody());
            throw new TransactionException('Transaction failed try again');
        }
    }
}