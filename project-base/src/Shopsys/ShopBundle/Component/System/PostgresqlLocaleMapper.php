<?php

namespace Shopsys\ShopBundle\Component\System;

/**
 * This class provides locale names for collations on different systems
 *
 * Unfortunately, in PostgreSQL locales are operating system dependent
 * which means that they can be different on each systems.
 * See https://www.postgresql.org/docs/9.6/static/collation.html for more details.
 *
 * Hopefully, this will get better with the introduction of ICU collations
 * in PostgreSQL v10 (https://www.postgresql.org/docs/10/static/collation.html).
 */
class PostgresqlLocaleMapper
{
    /**
     * @var string[]
     */
    private static $windowsLocalesIndexedByCollation = [
        'af_ZA' => 'Afrikaans_South Africa',
        'am_ET' => 'Amharic_Ethiopia',
        'ar_AE' => 'Arabic_United Arab Emirates',
        'ar_BH' => 'Arabic_Bahrain',
        'ar_DZ' => 'Arabic_Algeria',
        'ar_EG' => 'Arabic_Egypt',
        'ar_IQ' => 'Arabic_Iraq',
        'ar_JO' => 'Arabic_Jordan',
        'ar_KW' => 'Arabic_Kuwait',
        'ar_LB' => 'Arabic_Lebanon',
        'ar_LY' => 'Arabic_Libya',
        'ar_MA' => 'Arabic_Morocco',
        'ar_OM' => 'Arabic_Oman',
        'ar_QA' => 'Arabic_Qatar',
        'ar_SA' => 'Arabic_Saudi Arabia',
        'ar_SY' => 'Arabic_Syria',
        'ar_TN' => 'Arabic_Tunisia',
        'ar_YE' => 'Arabic_Yemen',
        'as_IN' => 'Assamese_India',
        'be_BY' => 'Belarusian_Belarus',
        'bg_BG' => 'Bulgarian_Bulgaria',
        'bn_BD' => 'Bangla_Bangladesh',
        'bn_IN' => 'Bangla_India',
        'br_FR' => 'Breton_France',
        'ca_ES' => 'Catalan_Spain',
        'cs_CZ' => 'Czech_Czech Republic',
        'cy_GB' => 'Welsh_United Kingdom',
        'da_DK' => 'Danish_Denmark',
        'de_AT' => 'German_Austria',
        'de_DE' => 'German_Germany',
        'de_CH' => 'German_Switzerland',
        'de_LI' => 'German_Liechtenstein',
        'de_LU' => 'German_Luxembourg',
        'dv_MV' => 'Divehi_Maldives',
        'el_GR' => 'Greek_Greece',
        'en_AU' => 'English_Australia',
        'en_CA' => 'English_Canada',
        'en_GB' => 'English_United Kingdom',
        'en_IE' => 'English_Ireland',
        'en_IN' => 'English_India',
        'en_NZ' => 'English_New Zealand',
        'en_PH' => 'English_Philippines',
        'en_SG' => 'English_Singapore',
        'en_US' => 'English_United States',
        'en_ZA' => 'English_South Africa',
        'en_ZW' => 'English_Zimbabwe',
        'es_AR' => 'Spanish_Argentina',
        'es_BO' => 'Spanish_Bolivia',
        'es_CL' => 'Spanish_Chile',
        'es_CO' => 'Spanish_Colombia',
        'es_CR' => 'Spanish_Costa Rica',
        'es_DO' => 'Spanish_Dominican Republic',
        'es_EC' => 'Spanish_Ecuador',
        'es_ES' => 'Spanish_Spain',
        'es_GT' => 'Spanish_Guatemala',
        'es_HN' => 'Spanish_Honduras',
        'es_MX' => 'Spanish_Mexico',
        'es_NI' => 'Spanish_Nicaragua',
        'es_PA' => 'Spanish_Panama',
        'es_PE' => 'Spanish_Peru',
        'es_PR' => 'Spanish_Puerto Rico',
        'es_PY' => 'Spanish_Paraguay',
        'es_SV' => 'Spanish_El Salvador',
        'es_US' => 'Spanish_United States',
        'es_UY' => 'Spanish_Uruguay',
        'es_VE' => 'Spanish_Venezuela',
        'et_EE' => 'Estonian_Estonia',
        'eu_ES' => 'Basque_Spain',
        'fa_IR' => 'Persian_Iran',
        'fi_FI' => 'Finnish_Finland',
        'fil_PH' => 'Filipino_Philippines',
        'fo_FO' => 'Faroese_Faroe Islands',
        'fr_BE' => 'French_Belgium',
        'fr_CA' => 'French_Canada',
        'fr_FR' => 'French_France',
        'fr_CH' => 'French_Switzerland',
        'fr_LU' => 'French_Luxembourg',
        'ga_IE' => 'Irish_Ireland',
        'gd_GB' => 'Scottish Gaelic_United Kingdom',
        'gl_ES' => 'Galician_Spain',
        'gu_IN' => 'Gujarati_India',
        'he_IL' => 'Hebrew_Israel',
        'hi_IN' => 'Hindi_India',
        'hr_HR' => 'Croatian_Croatia',
        'hsb_DE' => 'Upper Sorbian_Germany',
        'hu_HU' => 'Hungarian_Hungary',
        'hy_AM' => 'Armenian_Armenia',
        'id_ID' => 'Indonesian_Indonesia',
        'ig_NG' => 'Igbo_Nigeria',
        'is_IS' => 'Icelandic_Iceland',
        'it_CH' => 'Italian_Switzerland',
        'it_IT' => 'Italian_Italy',
        'ja_JP' => 'Japanese_Japan',
        'ka_GE' => 'Georgian_Georgia',
        'kk_KZ' => 'Kazakh_Kazakhstan',
        'kl_GL' => 'Greenlandic_Greenland',
        'km_KH' => 'Khmer_Cambodia',
        'kn_IN' => 'Kannada_India',
        'kok_IN' => 'Konkani_India',
        'ko_KR' => 'Korean_Korea',
        'ky_KG' => 'Kyrgyz_Kyrgyzstan',
        'lb_LU' => 'Luxembourgish_Luxembourg',
        'lo_LA' => 'Lao_Lao',
        'lt_LT' => 'Lithuanian_Lithuania',
        'lv_LV' => 'Latvian_Latvia',
        'mi_NZ' => 'Maori_New Zealand',
        'ml_IN' => 'Malayalam_India',
        'mr_IN' => 'Marathi_India',
        'ms_MY' => 'Malay_Malaysia',
        'mt_MT' => 'Maltese_Malta',
        'ne_NP' => 'Nepali_Nepal',
        'nl_BE' => 'Dutch_Belgium',
        'nl_NL' => 'Dutch_Netherlands',
        'nso_ZA' => 'Sesotho sa Leboa_South Africa',
        'oc_FR' => 'Occitan_France',
        'or_IN' => 'Odia_India',
        'pa_IN' => 'Punjabi_India',
        'pl_PL' => 'Polish_Poland',
        'ps_AF' => 'Pashto_Afghanistan',
        'pt_BR' => 'Portuguese_Brazil',
        'pt_PT' => 'Portuguese_Portugal',
        'quz_PE' => 'Quechua_Peru',
        'ro_RO' => 'Romanian_Romania',
        'ru_RU' => 'Russian_Russia',
        'rw_RW' => 'Kinyarwanda_Rwanda',
        'sa_IN' => 'Sanskrit_India',
        'se_NO' => 'Sami (Northern)_Norway',
        'si_LK' => 'Sinhala_Sri Lanka',
        'sk_SK' => 'Slovak_Slovakia',
        'sl_SI' => 'Slovenian_Slovenia',
        'sq_AL' => 'Albanian_Albania',
        'sv_FI' => 'Swedish_Finland',
        'sv_SE' => 'Swedish_Sweden',
        'sw_KE' => 'Kiswahili_Kenya',
        'ta_IN' => 'Tamil_India',
        'te_IN' => 'Telugu_India',
        'th_TH' => 'Thai_Thailand',
        'tk_TM' => 'Turkmen_Turkmenistan',
        'tn_ZA' => 'Setswana_South Africa',
        'tr_TR' => 'Turkish_Turkey',
        'tt_RU' => 'Tatar_Russia',
        'uk_UA' => 'Ukrainian_Ukraine',
        'vi_VN' => 'Vietnamese_Vietnam',
        'wo_SN' => 'Wolof_Senegal',
        'yo_NG' => 'Yoruba_Nigeria',
        'zh_SG' => 'Chinese (Simplified)_Singapore',
        'zh_TW' => 'Chinese (Traditional)_Taiwan',
    ];

    /**
     * @param string $collationName
     * @return string
     */
    public function getLinuxLocale($collationName)
    {
        return $collationName . '.utf8';
    }

    /**
     * @param string $collationName
     * @return string
     */
    public function getMacOsxLocale($collationName)
    {
        return $collationName . '.UTF-8';
    }

    /**
     * @param string $collationName
     * @return string
     */
    public function getWindowsLocale($collationName)
    {
        if (!array_key_exists($collationName, self::$windowsLocalesIndexedByCollation)) {
            throw new \Shopsys\ShopBundle\Component\System\Exception\UnknownWindowsLocaleException($collationName);
        }

        return self::$windowsLocalesIndexedByCollation[$collationName];
    }
}
