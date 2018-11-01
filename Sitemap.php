<?php

/*
 * WebLife CMS
 * Created on 01.11.2018, 10:19:11
 * Developed by http://weblife.ua/
 */

namespace Sitemap;

require dirname(__FILE__).DS."SitemapFactory.php";
require dirname(__FILE__).DS."SitemapDataProvider.php";
require dirname(__FILE__).DS."SitemapXML.php";
require dirname(__FILE__).DS."SitemapXMLNode.php";

use Sitemap\SitemapXML,
    Sitemap\DataProvider,
    Sitemap\SitemapDataProvider,
    Sitemap\CategoriesDataProvider,
    Sitemap\CatalogDataProvider,
    Sitemap\PrintsDataProvider,
    Sitemap\NewsDataProvider;

/**
 * Description of Sitemap
 *
 * @author user5
 */
interface Sitemap {

    public function getFilename ();

    public function setFilename ();

    public function export();
}

class SitemapInstance implements Sitemap {

    protected $XML;

    public function __construct () {
        $this->XML = new \Sitemap\SitemapXML();
    }

    public function getFilename () {
        return $this->XML->getFilename();
    }

    public function setFilename ($filename) {
        $this->XML->setFilename($filename);
    }

    public function export(DataProvider $DataProvider) {
        $this->XML->addItems($DataProvider->get());
        $this->XML->write();
    }
}