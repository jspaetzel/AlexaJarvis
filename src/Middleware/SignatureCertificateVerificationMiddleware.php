<?php
namespace Middleware;

use Models\Certificate\FileCertificateProvider;
use Models\Exceptions\InvalidCertificateException;
use Models\Exceptions\InvalidSignatureChainException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Request;

class SignatureCertificateVerificationMiddleware
{
    const CERTIFICATE_URL_HEADER = "Signaturecertchainurl";
    const SIGNATURE_HEADER       = "Signature";
    const ENCRYPT_METHOD         = "sha1WithRSAEncryption";

    const ALEXA_ORIGIN_HOST = "s3.amazonaws.com";
    const ALEXA_ORIGIN_PATH = "/echo.api/";
    const ALEXA_ORIGIN_SCHEME = "https";
    const ALEXA_ORIGIN_PORT = "443";

    const ALEXA_TIMESTAMP_TOLERANCE = 150;

    private $certificateProvider;

    public function __construct($root_directory)
    {
        $this->certificateProvider = new FileCertificateProvider($root_directory . '/certificates/');
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $certificateResult = $this->verifyCertificate($request);
        if ($certificateResult === 1) {
            return $next($request, $response);
        } elseif ($certificateResult = 0) {
            throw new InvalidSignatureChainException("The request did not validate against the certificate chain.");
        } else {
            throw new \Exception("Something went wrong when validating the request and certificate.");
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function getCertificate(Request $request)
    {
        $signatureChainUri = $request->getHeader(self::CERTIFICATE_URL_HEADER)[0];
        $this->validateKeychainUri($signatureChainUri);
        $certificate = $this->certificateProvider->getCertificateFromUri($signatureChainUri);
        return $certificate;
    }
    /**
     * @param string $keychainUri
     *
     * @throws InvalidCertificateException
     */
    private function validateKeychainUri($keychainUri)
    {
        $uriParts = parse_url($keychainUri);
        if (strcasecmp($uriParts['host'], self::ALEXA_ORIGIN_HOST) !== 0) {
            throw new InvalidCertificateException("The host for the Certificate provided in the header is invalid");
        }
        if (strpos($uriParts['path'], self::ALEXA_ORIGIN_PATH) !== 0) {
            throw new InvalidCertificateException("The URL path for the Certificate provided in the header is invalid");
        }
        if (strcasecmp($uriParts['scheme'], self::ALEXA_ORIGIN_SCHEME) !== 0) {
            throw new InvalidCertificateException("The URL is using an unsupported scheme. Should be https");
        }
        if (array_key_exists('port', $uriParts) && $uriParts['port'] != self::ALEXA_ORIGIN_PORT) {
            throw new InvalidCertificateException("The URL is using an unsupported https port");
        }
    }
    /**
     * @param Request $request
     *
     * @return string
     */
    private function getDecodedSignature(Request $request)
    {
        $signature = $request->getHeader(self::SIGNATURE_HEADER)[0];
        $base64DecodedSignature = base64_decode($signature);
        return $base64DecodedSignature;
    }

    /**
     * @param Request $request
     * @return int
     */
    private function verifyCertificate(Request $request)
    {
        $signature = $this->getDecodedSignature($request);
        $certificate = $this->getCertificate($request);
        //Get the request body that will be validated
        $data = $request->getBody();
        $certKey = openssl_pkey_get_public($certificate);
        // ok, let's do this thing! we're saving the world from hackers here!
        $valid = openssl_verify($data, $signature, $certKey, self::ENCRYPT_METHOD);
        return $valid;
    }
}