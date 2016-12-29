<?php
namespace Models\Contracts;
interface CertificateProvider
{
    /**
     * @param string $certificateChainUri
     *
     * @return string|null
     */
    public function getCertificateFromUri($certificateChainUri);
}