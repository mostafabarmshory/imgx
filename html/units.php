<?php
use Pluf\Imgx\Converter;
use Pluf\Imgx\Fetcher;
use Pluf\Imgx\FileToHttpResponse;
use Pluf\Imgx\OriginMaker;
use Pluf\Scion\Process\HttpProcess;
use Pluf\Imgx\UrlDownloader;
use Pluf\Imgx\UrlFetcher;
use Pluf\Http\UriFactory;

return [
    [
        new HttpProcess('#^/imgx#', [
            'GET'
        ]),
        FileToHttpResponse::class,
        [
            new HttpProcess('#^/api/v2/cms/contents/(?P<id>\d+)/content$#'),
            new Fetcher('/tmp'),
            OriginMaker::class,
            Converter::class
        ],
        [
            new HttpProcess('#^/(?P<url>http.+)#'),
            function ($url, $unitTracker) {
                $hosts = [
                    'elbaan.com',
                    '7tooti.com',
                    'viraweb123.ir',
                    'cdn.viraweb123.ir'
                ];
                $uriFactory = new UriFactory();
                $uri = $uriFactory->createUri($url);
                $host = $uri->getHost();
                if (! in_array($host, $hosts)) {
                    throw new \Exception('Unregisterd domain:' . $host);
                }
                return $unitTracker->next();
            },
            new UrlFetcher('/tmp'),
            UrlDownloader::class,
            Converter::class
        ]
    ],
    function () {
        throw new \Exception('Not implemented yet!');
    }
];