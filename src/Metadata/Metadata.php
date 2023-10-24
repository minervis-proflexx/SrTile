<?php

namespace srag\Plugins\SrTile\Metadata;

use ilMD;
use ilObject;
use ilSrTilePlugin;
use srag\DIC\SrTile\DICTrait;
use srag\DIC\SrTile\Version\PluginVersionParameter;
use srag\Plugins\SrTile\Utils\SrTileTrait;

/**
 * Class Metadata
 *
 * @package srag\Plugins\SrTile\Metadata
 */
class Metadata
{

    use SrTileTrait;
    use DICTrait;

    const PLUGIN_CLASS_NAME = ilSrTilePlugin::class;
    /**
     * @var self[]
     */
    protected static $instances = [];
    /**
     * @var ilObject
     */
    protected $il_object;


    /**
     * Metadata constructor
     *
     * @param ilObject $il_object
     */
    private function __construct(ilObject $il_object)
    {
        $this->il_object = $il_object;
    }


    /**
     * @param ilObject $il_object
     *
     * @return self
     */
    public static function getInstance(ilObject $il_object) : self
    {
        if (!isset(self::$instances[$il_object->getId()])) {
            self::$instances[$il_object->getId()] = new self($il_object);
        }

        return self::$instances[$il_object->getId()];
    }


    /**
     * @return string
     */
    public function getLanguageCode() : string
    {
        $il_md = new ilMD($this->il_object->getId(), $this->il_object->getId(), $this->il_object->getType());

        if (!$il_md->getGeneral()) {
            return "";
        }

        /**
         * var $md_language ilMDLanguage
         */
        $md_language = $il_md->getGeneral()->getLanguage(current($il_md->getGeneral()->getLanguageIds()));

        return ($md_language->getLanguageCode() !== false ? $md_language->getLanguageCode() : "");
    }


    /**
     * @return string
     */
    public function getLanguageImage() : string
    {
        $version_parameter = PluginVersionParameter::getInstance()->withPlugin(self::plugin());

        $mapping = [
            "en" => "gb"
        ];

        self::dic()->ui()->mainTemplate()->addCss($version_parameter->appendToUrl(self::plugin()->directory() . "/vendor/components/flag-icon-css/css/flag-icon.min.css"));

        $language_code = $this->getLanguageCode();

        if (!empty($language_code)) {
            $language_code = $mapping[$language_code] ?: $language_code;
            if ($language_code === "sq") {
                $language_code = "al";
            }

            return '<span class="flag-icon flag-icon-' . htmlspecialchars($language_code) . '"></span> ';
        }

        return "";
    }


    /**
     * @return string
     */
    public function getLanguageText() : string
    {
        $language_code = $this->getLanguageCode();

        if (!empty($language_code)) {
            self::dic()->language()->loadLanguageModule("meta");

            return self::dic()->language()->txt("meta_l_" . $language_code);
        }

        return "";
    }
}
