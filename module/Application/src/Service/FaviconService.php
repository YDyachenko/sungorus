<?php

namespace Application\Service;

use Application\Model\AccountData;

class FaviconService
{

    const FAVICON_PATH = 'data/favicons/%s.png';
    const DEFAULT_NAME = 'default';
    const SERVICE_URL  = 'https://www.google.com/s2/favicons?domain=%s';

    public function getFileFromAccount(AccountData $accountData)
    {
        $data = $accountData->getData();

        return $this->getFileFromURL($data['url']);
    }

    public function getFileFromURL($url)
    {
        $filepath = sprintf(self::FAVICON_PATH, self::DEFAULT_NAME);

        if (!($host = parse_url($url, PHP_URL_HOST))) {
            return $filepath;
        }

        $newFile = sprintf(self::FAVICON_PATH, md5($host));
        if (file_exists($newFile)) {
            return $newFile;
        }

        $data = file_get_contents(sprintf(self::SERVICE_URL, urlencode($host)));
        if ($data) {
            file_put_contents($newFile, $data);
            $filepath = $newFile;
        }

        return $filepath;
    }

}
