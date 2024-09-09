<?php

namespace Uniscale\PrivateDemoPhpBackend;

use Exception;
use Uniscale\Platform\PlatformSession;

/**
 * Base Service class that handles common server logic such as
 * request parsing, CORS handling, and session management.
 *
 * This class serves as a parent for specific service implementations.
 */
abstract class Service
{
    public readonly string $serviceName;
    protected PlatformSession $session;
    protected readonly int $port;
    protected readonly string $address;

    /**
     * Constructor to initialize the service with a port and name.
     *
     * @param int $port The port number to listen on.
     * @param string $serviceName The name of the service.
     */
    public function __construct(int $port, string $serviceName)
    {
        $this->port = $port;
        $this->address = '0.0.0.0';
        $this->serviceName = $serviceName;
        $this->initializeSession();
    }

    /**
     * Initializes the session. Must be implemented by child classes.
     *
     * @return void
     */
    abstract protected function initializeSession(): void;

    /**
     * Starts the service, listening on the specified port.
     *
     * @return void
     * @throws Exception If the socket cannot be created or bound.
     */
    public function start(): void
    {
        $socket = stream_socket_server("tcp://{$this->address}:{$this->port}", $errno, $errstr);
        if (!$socket) {
            throw new Exception("Error creating socket: $errstr ($errno)");
        }

        echo "{$this->serviceName} service listening on port {$this->port}...\n";

        while (true) {
            $conn = @stream_socket_accept($socket);
            if ($conn) {
                $request = fread($conn, 4096);
                $response = $this->handleRequest($request);
                fwrite($conn, $response);
                fclose($conn);
            }
        }

        fclose($socket);
    }

    /**
     * Handles the incoming request and returns a response.
     *
     * @param string $request The raw HTTP request received by the server.
     * @return string The HTTP response to send back to the client.
     * @throws Exception
     */
    protected function handleRequest(string $request): string
    {
        $parsedRequest = $this->parseRequest($request);

        // Handle CORS preflight requests
        if ($parsedRequest['method'] === 'OPTIONS') {
            return $this->handleCorsOptionsRequest();
        }

        // Process the request content via the session
        $result = $this->session->acceptGatewayRequest($parsedRequest['body']);

        // Construct the response with CORS headers
        $response = "HTTP/1.1 " . $result->getStatusCode() . " OK\r\n";
        $response .= $this->getCorsHeaders();
        $response .= "Content-Type: application/json\r\n";
        $response .= "\r\n";
        $response .= json_encode($result->jsonSerialize());

        return $response;
    }

    /**
     * Parses the raw HTTP request into components.
     *
     * @param string $request The raw HTTP request string.
     * @return array An associative array containing the method, headers, and body.
     */
    private function parseRequest(string $request): array
    {
        $parts = explode("\r\n\r\n", $request, 2);
        $headers = $parts[0] ?? '';
        $body = $parts[1] ?? '';

        $lines = explode("\r\n", $headers);
        $requestLine = array_shift($lines);

        $method = explode(' ', $requestLine)[0] ?? 'GET';  // Default to GET if the method is not found

        return [
            'method' => strtoupper($method),
            'headers' => $lines,
            'body' => $body
        ];
    }

    /**
     * Handles CORS preflight (OPTIONS) requests.
     *
     * @return string The HTTP response for CORS preflight.
     */
    private function handleCorsOptionsRequest(): string
    {
        $response = "HTTP/1.1 204 No Content\r\n";
        $response .= $this->getCorsHeaders();
        $response .= "Content-Length: 0\r\n";
        $response .= "\r\n";
        return $response;
    }

    /**
     * Returns CORS headers to be included in responses.
     *
     * @return string The CORS headers.
     */
    private function getCorsHeaders(): string
    {
        return implode("\r\n", [
                "Access-Control-Allow-Origin: *",
                "Access-Control-Allow-Methods: POST, GET, OPTIONS",
                "Access-Control-Allow-Headers: Content-Type, Authorization"
            ]) . "\r\n";
    }
}
