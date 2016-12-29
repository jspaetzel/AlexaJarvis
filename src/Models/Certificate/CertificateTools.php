<?php

namespace Models\Certificate;

trait CertificateTools
{
    /**
     * @param mixed $certificate
     *
     * @return array
     */
    protected function parseCertificate($certificate)
    {
        return openssl_x509_parse($certificate);
    }

    /**
     * Returns whether the configured service domain is present and valid
     *
     * @param array $parsedCertificate
     *
     * @param $amazonServiceDomain
     * @return bool
     */
    protected function verifyCertificateSubjectAltNamePresent(array $parsedCertificate, $amazonServiceDomain)
    {
        return strpos($parsedCertificate['extensions']['subjectAltName'], $amazonServiceDomain) !== false;
    }

    /**
     * Returns whether the date is valid
     *
     * @param array $parsedCertificate
     *
     * @return bool
     */
    protected function validateCertificateDate(array $parsedCertificate)
    {
        $validFrom = $parsedCertificate['validFrom_time_t'];

        $validTo = $parsedCertificate['validTo_time_t'];

        $time = time();

        return ($validFrom <= $time && $time <= $validTo);
    }

    /**
     * @param string $certificateChainUri
     *
     * @return string
     */
    protected function getRemoteCertificateChain($certificateChainUri)
    {
        return file_get_contents($certificateChainUri);
    }
}
