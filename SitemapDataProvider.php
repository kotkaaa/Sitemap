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

    protected $items;
    protected $DB;
    protected $UrlWL;

    public function __construct (\DbConnector $DB, \UrlWL $UrlWL) {
        $this->DB    = $DB;
        $this->UrlWL = $UrlWL;
    }
}

class CategoriesDataProvider extends BaseDataProvider implements DataProvider {
    /**
     *
     * @return array
     */
    public function get() {
        $query = "SELECT * FROM `".MAIN_TABLE."` "
                . "WHERE `active`>0 AND `id`>8 AND (`redirectid` + LENGTH(`redirecturl`))=0 "
                . "ORDER BY `id`";
        $this->DB->Query($query);
        if ($this->DB->getNumRows()) {
            while ($row = $this->DB->fetchAssoc()) {
                $this->items[] = $row;
            }
        } $this->DB->Free();
        return $this->items;
    }
}

class CatalogDataProvider extends BaseDataProvider implements DataProvider {

    public function get();
}

class PrintsDataProvider extends BaseDataProvider implements DataProvider {

    public function get();
}

class NewsDataProvider extends BaseDataProvider implements DataProvider {

    public function get();
}