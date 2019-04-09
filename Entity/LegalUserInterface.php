<?php

namespace Troopers\MangopayBundle\Entity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Defines mandatory methods a Mango user should have
 * https://docs.mangopay.com/api-references/users/legal-users/.
 */
interface LegalUserInterface extends UserInterface
{
    /**
     * @var string
     *             Business name. (<100 chars)
     */
    public function getName();

    /**
     * @var string
     *             The type of legal user
     */
    public function getLegalPersonType();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getHeadquartersStreetAddress();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getHeadquartersAdditionalStreetAddress();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getHeadquartersCity();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getHeadquartersCountry();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getHeadquartersPostalCode();

    /**
     * @var string
     *             The firstname of the company’s Legal representative person (<100 chars)
     */
    public function getLegalRepresentativeFirstName();

    /**
     * @var string
     *             The lastname of the company’s Legal representative person (<100 chars)
     */
    public function getLegalRepresentativeLastName();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getLegalRepresentativeAddress();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getLegalRepresentativeStreetAddress();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getLegalRepresentativeAdditionalStreetAddress();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getLegalRepresentativeCity();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getLegalRepresentativeCountry();

    /**
     * @var string
     *             The address of the company’s Legal representative person
     */
    public function getLegalRepresentativePostalCode();

    /**
     * @var string
     *             The email of the company’s Legal representative person - must be a valid
     */
    public function getLegalRepresentativeEmail();

    /**
     * @var date
     *           The date of birth of the company’s Legal representative person - be careful to set the right timezone
     * (should be UTC) to avoid 00h becoming 23h (and hence interpreted as the day before)
     */
    public function getLegalRepresentativeBirthday();

    /**
     * @var string
     *             The nationality of the company’s Legal representative person.
     * ISO 3166-1 alpha-2 format is expected
     */
    public function getLegalRepresentativeNationality();

    /**
     * @var string
     *             The country of residence of the company’s Legal representative person.
     * ISO 3166-1 alpha-2 format is expected
     */
    public function getLegalRepresentativeCountryOfResidence();

    /**
     * @var string
     *             The official registered number of the business
     */
    public function getCompanyNumber();

    /**
     * @var string
     *             ID Card, Passport or driving licence for SEPA area. Passeport or driving licence for the UK, USA and
     *             Canada. For other nationalities a passport is required.
     *             In the case of a legal user, this document should refer to the individual duly empowered to act on
     *             behalf of the legal entity
     */
    public function  getLegalRepresentativeProofOfIdentityId();
    public function  setLegalRepresentativeProofOfIdentityId($id);

    /**
     * @var string
     *             Certified articles of association (Statute) - formal memorandum stated by the entrepreneurs, in which
     *             the following information is mentioned: business name, activity, registered address, shareholding…
     */
    public function  getStatuteId();
    public function  setStatuteId($id);

    /**
     * @var string
     *             Extract from the Company Register issued within the last three months
     *             In the case of an organization or soletrader, this can be a proof of registration from the official
     *             authority
     */
    public function  getProofOfRegistrationId();
    public function  setProofOfRegistrationId($id);

    /**
     * @var string
     *             Shareholder declaration (as https://www.mangopay.com/terms/shareholder-declaration/Shareholder_Declaration-EN.pdf)
     */
    public function  getShareholderDeclarationId();
    public function  setShareholderDeclarationId($id);

    /**
     * @var File
     *             ID Card, Passport or driving licence for SEPA area. Passeport or driving licence for the UK, USA and
     *             Canada. For other nationalities a passport is required.
     *             In the case of a legal user, this document should refer to the individual duly empowered to act on
     *             behalf of the legal entity
     */
    public function  getLegalRepresentativeProofOfIdentity();

    /**
     * @var File
     *             Certified articles of association (Statute) - formal memorandum stated by the entrepreneurs, in which
     *             the following information is mentioned: business name, activity, registered address, shareholding…
     */
    public function  getStatute();

    /**
     * @var File
     *             Extract from the Company Register issued within the last three months
     *             In the case of an organization or soletrader, this can be a proof of registration from the official
     *             authority
     */
    public function  getProofOfRegistration();

    /**
     * @var File
     *             Shareholder declaration (as https://www.mangopay.com/terms/shareholder-declaration/Shareholder_Declaration-EN.pdf)
     */
    public function  getShareholderDeclaration();

}
