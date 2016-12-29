<?php

namespace Models\Certificate;

use Models\Contracts\CertificateProvider;

class FileCertificateProvider extends BaseCertificateProvider implements CertificateProvider
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * FileCertificateProvider constructor.
     *
     * @param string     $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Persist the certificate contents to data store so it's retrievable using the certificate chain uri
     *
     * @param string $certificateChainUri
     * @param string $certificateContents
     */
    protected function persistCertificate($certificateChainUri, $certificateContents)
    {
        file_put_contents($this->calculateFilePath($certificateChainUri), $certificateContents);
    }

    /**
     * Retrieve the certificate give the certificate chain's uri from the datastore
     *
     * @param string $certificateChainUri
     *
     * @return string|null
     */
    protected function retrieveCertificateFromStore($certificateChainUri)
    {
        $certificateChain = file_get_contents($this->calculateFilePath($certificateChainUri));
        if ( $certificateChain === false ) {
            $certificateChain = null;
        }

        return $certificateChain;
    }

    /**
     * Calculate the path that the certificate should be stored
     *
     * @param string $certificateChainUri
     *
     * @return string
     */
    private function calculateFilePath($certificateChainUri)
    {
        $filename = md5($certificateChainUri);

        $path = $this->filePath.$filename;

        return $path;
    }
}