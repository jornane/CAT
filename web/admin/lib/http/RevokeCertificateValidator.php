<?php
namespace lib\http;

use lib\domain\SilverbulletCertificate;

class RevokeCertificateValidator extends AbstractCommandValidator{

    const COMMAND = 'revokecertificate';

    /**
     *
     * {@inheritDoc}
     * @see \lib\http\AbstractCommand::execute()
     */
    public function execute(){
        $certificate = SilverbulletCertificate::prepare($_POST[self::COMMAND]);
        $certificate->setRevoked(true);
        $certificate->save();
        if($certificate->isGenerated()){
            $profile = $this->factory->getProfile();
            $profile->revokeCertificate($certificate->getSerialNumber());
        }
        $this->factory->redirectAfterSubmit();
    }

}
