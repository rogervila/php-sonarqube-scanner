<?php

namespace Tests\Sonar\Helpers;

use Creativestyle\AppHttpServerMock\Server;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestServer extends Server
{
    protected function registerRequestHandlers()
    {
        $this->registerRequestHandler(['GET','PUT', 'POST'], '/', function (Request $request) {
            return new Response('TODO');
        });

        $this->registerRequestHandler(['GET','PUT', 'POST'], '/batch/index', function (Request $request) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?> <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
            $xml .= '<mynode><content>TODO</content></mynode>';

            $response = new Response($xml);
            $response->headers->set('Content-Type', 'xml');

            return $response;
        });
    }
}
