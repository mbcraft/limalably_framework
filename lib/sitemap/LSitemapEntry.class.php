<?php

<?

define ("CRLF","\r\n");
define ("T","\t");

class LSitemapEntry
{
	private $host;
    private $loc;
    private $changefreq;
    private $lastmod;
    private $priority;

    const CHANGEFREQ_ALWAYS = "always";
    const CHANGEFREQ_HOURLY = "hourly";
    const CHANGEFREQ_DAILY = "daily";
    const CHANGEFREQ_WEEKLY = "weekly";
    const CHANGEFREQ_MONTHLY = "monthly";
    const CHANGEFREQ_YEARLY = "yearly";
    const CHANGEFREQ_NEVER = "never";

    public function setHost(string $host) {
    	$this->host = $host;
    }

    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    public function setChangeFreq($changefreq)
    {
        $this->changefreq = $changefreq;
    }

    public function setLastMod($lastmod)
    {
        $this->lastmod = $lastmod;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    function render()
    {
        echo T."<url>".CRLF;
        echo T.T."<loc>".$this->host.$this->loc."</loc>".CRLF;
        echo T.T."<lastmod>".$this->lastmod."</lastmod>".CRLF;
        echo T.T."<changefreq>".$this->changefreq."</changefreq>".CRLF;
        echo T.T."<priority>".$this->priority."</priority>".CRLF;
        echo T."</url>".CRLF;
    }
}

?>