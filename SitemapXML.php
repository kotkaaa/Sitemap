<?php

/*
 * WebLife CMS
 * Created on 01.11.2018, 10:22:17
 * Developed by http://weblife.ua/
 */

namespace Sitemap;

use Sitemap\SitemapXMLNode;

interface SitemapXMLInterface {

    public function header();

    public function nodes();

    public function footer();

    public function write();

    public function flush();

    public function open();

    public function close();
}

/**
 * Description of SitemapXML
 *
 * @author user5
 */
class SitemapXML implements SitemapXMLInterface {

    private $fp;
    private $code;

    protected $filename;
    protected $items;

    public function __construct ($filename) {
        $this->filename = $filename;
    }

    public function setFilename ($filename) {
        $this->filename = $filename;
    }

    public function getFilename () {
        return $this->filename;
    }

    public function addItems (array $items = []) {
        $this->items = $items;
    }

    private function header () {
        $this->code .= '<?xml version="1.0" encoding="UTF-8"?>
                            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
                            <!-- Last update of sitemap ' . date('c') . ' -->';
    }
    
    private function footer () {
        $this->code .= '</urlset>';
    }
    
    public function nodes () {
        $i = 0;
        $node = new SitemapXMLNode();
        do {
            $node->setLoc($this->items[$i]["url"]);
            $this->code .= $node->toXml();
            unset($this->items[$i]);
            $i++;
        } while (!empty($this->items));
        unset($node);
    }

    public function write () {
        // open file
        $this->open();
        // add header
        $this->header();
        // add nodes
        $this->nodes();
        // add footer
        $this->footer();
        // write file
        @fwrite($this->fp, $this->code);
        // close file & flush data
        $this->close();
    }

    public function open () {
        try {
            $this->fp = @fopen($this->filename, 'a+');
        } catch (\Exception $ex) {
            print nl2br($ex->getMessage());
        }
    }

    public function close () {
        fclose($this->fp);
        $this->flush();
    }

    public function flush() {
        $this->code = null;
        $this->fp = null;
        sleep(1);
    }
}
