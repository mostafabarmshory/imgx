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
    FileToHttpResponse::class,
    [
        new HttpProcess('#^/imgx/api/v2/cms/contents/(?P<id>\d+)/content$#', [
            'GET'
        ]),
        new Fetcher('/tmp'),
        OriginMaker::class,
        Converter::class
    ],
    [
        new HttpProcess('#^/(?P<url>http.+)$#', [
            'GET'
        ]),
        function ($url, $unitTracker) {
            $hostEnv = getenv("IMGX_ALLOWED_HOST");
            if ("*" !== $hostEnv) {
                $hosts = [
                    $hostEnv
                    // NOTE: add other host here
                ];
                $uriFactory = new UriFactory();
                $uri = $uriFactory->createUri($url);
                $host = $uri->getHost();
                if (! in_array($host, $hosts)) {
                    throw new \Exception('Unregisterd domain:' . $hostEnv . '<' . $host);
                }
            }
            return $unitTracker->next();
        },
        new UrlFetcher('/tmp'),
        UrlDownloader::class,
        Converter::class
    ],
    function () {
        throw new \Exception('Not implemented yet!');
    }
];