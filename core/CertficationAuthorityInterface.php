<?php

/*
 * ******************************************************************************
 * Copyright 2011-2017 DANTE Ltd. and GÉANT on behalf of the GN3, GN3+, GN4-1 
 * and GN4-2 consortia
 *
 * License: see the web/copyright.php file in the file structure
 * ******************************************************************************
 */

namespace core;

interface CertificationAuthorityInterface {
    /**
     * create a CSR
     * 
     * @param resource $privateKey the private key to create the CSR with
     * @param string   $fed        the federation to which the certificate belongs
     * @param string   $username   the future username
     * @return array with the CSR and some meta info
     */
    public function generateCompatibleCsr($privateKey, $fed, $username) : array;
    
    /**
     * Creates an updated OCSP statement
     * 
     * @param \core\SilverbulletCertificate $cert
     * @return array
     */
    public function triggerNewOCSPStatement(SilverbulletCertificate $cert) : string;
    
    /**
     * signs a certificate request
     * 
     * @param array $csr the array with the CSR and meta info as generated in generateCompatibleCsr()
     * @param int $expiryDays how many days should the cert be valid
     * @return array information about the signed certificate
     */
    public function signRequest($csr, $expiryDays) : array;
    
    /**
     * revokes a certificate
     * 
     * @param \core\SilverbulletCertificate $cert
     * @return void
     */
    public function revokeCertificate(SilverbulletCertificate $cert) : void;
}