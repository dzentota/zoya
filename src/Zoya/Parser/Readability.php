<?php

namespace Zoya\Parser;

use Valera\Content;
use Valera\Parser\ParserInterface;
use Valera\Parser\Result;
use Readability as ZoyaReadability;

class Readability implements ParserInterface
{

    public function parse(Content $content, Result $result)
    {
        $html = $content->getContent();
        if (function_exists('tidy_parse_string')) {
            $tidy = tidy_parse_string($html, ['output-xhtml' => true], 'UTF8');
            $tidy->cleanRepair();
            $html = $tidy->value;
        }
        $readability = new ZoyaReadability($html);
        $readability->init();
        $id = sha1($content->getSource()->getResource()->getUrl());
        $result->addDocument(
            $id,
            ['title' => $readability->getTitle()->innerHTML, 'content' => $readability->articleContent->innerHTML]
        );

    }
}
