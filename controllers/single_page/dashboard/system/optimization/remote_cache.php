<?php

namespace Concrete\Package\MdRemoteCacheCleaner\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Http\Client\Client;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use League\Url\Url;

class RemoteCache extends DashboardPageController
{
    public function view()
    {
        $config = $this->getConfig();
        $this->set('remote', $config->get('server.remote'));
        $this->set('key', $config->get('server.key'));

        $this->set('dh', $this->app->make('helper/date'));
    }

    public function save_server()
    {
        if (!$this->token->validate('save_server')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $remote = $this->post('remote');
        if (!$remote) {
            $this->error->add(t('Please input remote host.'));
        }

        $key = $this->post('key');
        if (!$key) {
            $this->error->add(t('Please input key.'));
        }

        if (!$this->error->has()) {
            $config = $this->getConfig();
            $config->save('server.remote', $remote);
            $config->save('server.key', $key);
            $this->flash('success', t('Successfully saved.'));

            return $this->buildRedirect('/dashboard/system/optimization/remote_cache');
        }
    }

    public function check_remote()
    {
        if (!$this->token->validate('check_remote')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $path = $this->get('path');
        if (!$path) {
            $this->error->add(t('Please input check path.'));
        }

        if (!$this->error->has()) {
            $config = $this->getConfig();
            $remote = $config->get('server.remote');
            $key = $config->get('server.key');
            $url = Url::createFromUrl($remote);
            if ($this->get('check')) {
                $url->setPath('/ccm/md_remote_cache_cleaner/check_cache');
                $url->setQuery([
                    'key' => $key,
                    'path' => $path,
                ]);
                /** @var Client $client */
                $client = $this->app->make('http/client');
                $response = $client->request('GET', (string) $url);
                if ($response->getStatusCode() === 200) {
                    $json = json_decode($response->getBody()->getContents());
                    $this->set('response', $json);
                } else {
                    $this->error->add($response->getReasonPhrase());
                }
            }
            if ($this->get('clear')) {
                $url->setPath('/ccm/md_remote_cache_cleaner/clear_cache');
                /** @var Client $client */
                $client = $this->app->make('http/client');
                $response = $client->request('POST', (string) $url, [
                    'form_params' => [
                        'key' => $key,
                        'path' => $path,
                    ],
                ]);
                if ($response->getStatusCode() === 200) {
                    $json = json_decode($response->getBody()->getContents());
                    $this->set('response', $json);
                } else {
                    $this->error->add($response->getReasonPhrase());
                }
            }
        }

        $this->view();
    }

    protected function getConfig(): ?\Concrete\Core\Config\Repository\Liaison
    {
        /** @var PackageService $service */
        $service = $this->app->make(PackageService::class);
        $class = $service->getClass('md_remote_cache_cleaner');

        return $class->getDatabaseConfig();
    }
}
