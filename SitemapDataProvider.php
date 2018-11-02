<?php

/*
 * WebLife CMS
 * Created on 01.11.2018, 11:18:17
 * Developed by http://weblife.ua/
 */

namespace Sitemap;

interface DataProvider {

    public function get();
}

/**
 * Description of SitemapDataProvider
 *
 * @author user5
 */
class SitemapDataProvider {

    public static function getItems(DataProvider $DataProvider) {
        return $DataProvider->get();
    }
}

class BaseDataProvider {

    const BRANDS_CATID  = 39;
    const CATALOG_CATID = 18;

    protected $items;
    protected $DB;
    protected $UrlWL;

    public function __construct (\DbConnector $DB, \UrlWL $UrlWL) {
        $this->DB    = $DB;
        $this->UrlWL = $UrlWL;
    }

    public function free () {
        $this->items = [];
    }
}

class CategoriesDataProvider extends BaseDataProvider implements DataProvider {

    protected $unSupportedModules = [
        "thanks", "checkout", "basket", "error",
        "callback", "request", "subscribe",
    ];
    /**
     *
     * @return array
     */
    public function get() {
        $this->main();
        $this->news();
        return $this->items;
    }

    private function main () {
        $query = "SELECT `id`, `redirectid`, `redirecturl`, `title`, `seo_path`, `pagetype`, `menutype`, `module`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".MAIN_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 AND `id`>8 " . PHP_EOL
                . "AND (`redirectid` + LENGTH(`redirecturl`)) = 0 " . PHP_EOL
                . "AND LENGTH(`seo_path`) > 0 " . PHP_EOL
                . "AND `module` NOT IN('" . implode("','", $this->unSupportedModules) . "')" . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            while ($row = $this->DB->fetchObject()) {
                $row->loc      = $this->UrlWL->buildCategoryUrl((array)$row);
                $this->items[] = $row;
            }
        } $this->DB->Free();
    }

    private function news () {
        $query = "SELECT `id`, `cid`, `title`, `seo_path`, `active`, " . PHP_EOL
                . "NULL AS `loc`, '7' AS `priority` FROM `".NEWS_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 " . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            $arCategory = [];
            while ($row = $this->DB->fetchObject()) {
                if (empty($arCategory)) $arCategory = $this->UrlWL->getCategoryById ($row->cid);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
            } unset($arCategory);
        } $this->DB->Free();
    }
}

class CatalogDataProvider extends BaseDataProvider implements DataProvider {

    public function get() {
        $this->brands();
        $this->catalog();
        return $this->items;
    }
    
    private function brands () {
        $query = "SELECT `id`, `title`, `seo_path`, `active`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".BRANDS_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 " . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            $arCategory = [];
            while ($row = $this->DB->fetchObject()) {
                if (empty($arCategory)) $arCategory = $this->UrlWL->getCategoryById (parent::BRANDS_CATID);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
            } unset($arCategory);
        } $this->DB->Free();
    }
    
    private function catalog () {
        $query = "SELECT `id`, `title`, `seo_path`, `active`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".CATALOG_TABLE."` " . PHP_EOL
                . "WHERE `active`>0 " . PHP_EOL
                . "ORDER BY `id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            $arCategory = [];
            while ($row = $this->DB->fetchObject()) {
                if (empty($arCategory)) $arCategory = $this->UrlWL->getCategoryById (parent::BRANDS_CATID);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
            } unset($arCategory);
        } $this->DB->Free();
    }
}

class PrintsDataProvider extends BaseDataProvider implements DataProvider {

    public function get() {
        $this->prints();
        return $this->items;
    }

    private function prints () {
        $query = "SELECT p.`id`, p.`category_id`, p.`title`, p.`active`, " . PHP_EOL
                . "(CONCAT(pt.`seo_path`, '" . \UrlWL::URL_SEPARATOR . "', p.`seo_path`)) AS `seo_path`, " . PHP_EOL
                . "NULL AS `loc`, '9' AS `priority` FROM `".PRINTS_TABLE."` p " . PHP_EOL
                . "LEFT JOIN `".PRODUCT_TYPES_TABLE."` pt ON(pt.`id` = p.`type_id`) " . PHP_EOL
                . "WHERE p.`active`>0 " . PHP_EOL
                . "GROUP BY p.`id`";
        $this->DB->Query($query) or die (mysql_error());
        if ($this->DB->getNumRows()) {
            $arCategory = [];
            while ($row = $this->DB->fetchObject()) {
                $arCategory    = $this->UrlWL->getCategoryById ($row->category_id);
                $row->loc      = $this->UrlWL->buildItemUrl($arCategory, (array)$row);
                $this->items[] = $row;
            } unset($arCategory);
        } $this->DB->Free();
    }
}

class FiltersDataProvider extends BaseDataProvider implements DataProvider {

    public function get() {}
}