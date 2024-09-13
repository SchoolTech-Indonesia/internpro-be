<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class MessageResource extends JsonResource
{
    protected bool $status;
    protected string $message;
    protected ?string $error;

    public function __construct($resource, bool $status, string $message, string $error = null)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
        $this->error = $error;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            /**
             * The response status.
             * @var boolean $status
             * @example true
             */
            'status' => $this->status,
            /**
             * The response message.
             * @var string $message
             * @example The data is successfully updated
             */
            'message' => $this->message,
        ];
        if (isset($this->error)) {
            $error = [
                /**
                 * The response error.
                 * @var string $error
                 * @example SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'test' for key 'partners_name_unique'
                 */
                'error' => $this->error];
            $response = array_merge($response, $error);
        }
        return $response;
    }

    /**
     * Customize the response for a resource.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setData($this->toArray($request));
    }
}
