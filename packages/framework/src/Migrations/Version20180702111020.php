<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180702111020 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->setInputPriceType();
        $this->setRoundingType();
        $this->setOrderSubmittedText();
        $this->setMainAdminMail();
        $this->setMainAdminMailName();
        $this->setFreeTransportAndPaymentPriceLimit();
        $this->setSeoMetaDescriptionMainPage();
        $this->setSeoTitleMainPage();
        $this->setSeoTitleAddOn();
        $this->setTermsAndConditionsArticleId();
        $this->setCookiesArticleId();
        $this->setDomainDataCreated();
        $this->setFeedHash();
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }

    private function setInputPriceType()
    {
        $inputPriceTypeSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'inputPriceType\' AND domain_id = 0;')->fetchColumn(0);
        if ($inputPriceTypeSettingCount <= 0) {
            /**
             * value 2 stands for INPUT_PRICE_TYPE_WITHOUT_VAT
             * @see \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
             */
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'inputPriceType\', 0, 2, \'integer\')');
        }
    }

    private function setRoundingType()
    {
        $roundingTypeSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'roundingType\' AND domain_id = 0;')->fetchColumn(0);
        if ($roundingTypeSettingCount <= 0) {
            /**
             * value 3 stands for ROUNDING_TYPE_INTEGER
             * @see \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
             */
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'roundingType\', 0, 3, \'integer\')');
        }
    }

    private function setOrderSubmittedText()
    {
        $orderSubmittedTextSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'orderSubmittedText\' AND domain_id = 1;')->fetchColumn(0);
        if ($orderSubmittedTextSettingCount <= 0) {
            $orderSubmittedText = '
                <p>
                    Order number {number} has been sent, thank you for your purchase.
                    We will contact you about next order status. <br /><br />
                    <a href="{order_detail_url}">Track</a> the status of your order. <br />
                    {transport_instructions} <br />
                    {payment_instructions} <br />
                </p>
            ';
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'orderSubmittedText\', 1, :text, \'string\')', [
                'text' => $orderSubmittedText,
            ]);
        }
    }

    private function setMainAdminMail()
    {
        $mailAdminMailSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'mainAdminMail\' AND domain_id = 1;')->fetchColumn(0);
        if ($mailAdminMailSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'mainAdminMail\', 1, \'no-reply@shopsys.com\', \'string\')');
        }
    }

    private function setMainAdminMailName()
    {
        $mainAdminMailNameSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'mainAdminMailName\' AND domain_id = 1;')->fetchColumn(0);
        if ($mainAdminMailNameSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'mainAdminMailName\', 1, \'Shopsys\', \'string\')');
        }
    }

    private function setFreeTransportAndPaymentPriceLimit(): void
    {
        $freeTransportAndPaymentPriceLimitSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'freeTransportAndPaymentPriceLimit\' AND domain_id = 1;')->fetchColumn(0);
        if ($freeTransportAndPaymentPriceLimitSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'freeTransportAndPaymentPriceLimit\', 1, null, \'none\')');
        }
    }

    private function setSeoMetaDescriptionMainPage()
    {
        $seoMetaDescriptionMainPageSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'seoMetaDescriptionMainPage\' AND domain_id = 1;')->fetchColumn(0);
        if ($seoMetaDescriptionMainPageSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'seoMetaDescriptionMainPage\', 1, :text, \'string\')', [
                'text' => 'Shopsys Framework - the best solution for your eshop.',
            ]);
        }
    }

    private function setSeoTitleMainPage()
    {
        $seoTitleMainPageSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'seoTitleMainPage\' AND domain_id = 1;')->fetchColumn(0);
        if ($seoTitleMainPageSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'seoTitleMainPage\', 1, :text, \'string\')', [
                'text' => 'Shopsys Framework - Title page',
            ]);
        }
    }

    private function setSeoTitleAddOn()
    {
        $seoTitleAddOnSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'seoTitleAddOn\' AND domain_id = 1;')->fetchColumn(0);
        if ($seoTitleAddOnSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'seoTitleAddOn\', 1, :text, \'string\')', [
                'text' => '| Demo eshop',
            ]);
        }
    }

    private function setTermsAndConditionsArticleId()
    {
        $termsAndConditionsArticleIdSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'termsAndConditionsArticleId\' AND domain_id = 1;')->fetchColumn(0);
        if ($termsAndConditionsArticleIdSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'termsAndConditionsArticleId\', 1, null, \'integer\')');
        }
    }

    private function setCookiesArticleId()
    {
        $cookiesArticleIdSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'cookiesArticleId\' AND domain_id = 1;')->fetchColumn(0);
        if ($cookiesArticleIdSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'cookiesArticleId\', 1, null, \'integer\')');
        }
    }

    private function setDomainDataCreated()
    {
        $domainDataCreatedSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'domainDataCreated\' AND domain_id = 1;')->fetchColumn(0);
        if ($domainDataCreatedSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'domainDataCreated\', 1, \'true\', \'boolean\')');
        }
    }

    private function setFeedHash()
    {
        $feedHashSettingCount = $this->sql('SELECT COUNT(*) FROM setting_values WHERE name = \'feedHash\' AND domain_id = 0;')->fetchColumn(0);
        if ($feedHashSettingCount <= 0) {
            $this->sql('INSERT INTO setting_values (name, domain_id, value, type) VALUES (\'feedHash\', 0, :hash, \'string\')', [
                'hash' => $this->generateTenCharactersHash(),
            ]);
        }
    }

    /**
     * Copy pasted, @see \Shopsys\FrameworkBundle\Component\String\HashGenerator::generateHash()
     * @return string
     */
    private function generateTenCharactersHash()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $numberOfChars = strlen($characters);

        $hash = '';
        for ($i = 1; $i <= 10; $i++) {
            $randomIndex = random_int(0, $numberOfChars - 1);
            $hash .= $characters[$randomIndex];
        }

        return $hash;
    }
}
