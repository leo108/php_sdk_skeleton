# PHP SDK Skeleton

A skeleton for PHP SDK development.

## Installation

`composer require leo108/php_sdk_skeleton -vvv`

## Quick Start

Let's get started with creating a simple github sdk using [REST Api](https://developer.github.com/v3/).

Create a class extends `Leo108\SDK\AbstractApi`,
override the `getFullApiUrl` method.

```
class RepositoryApi extends Leo108\SDK\AbstractApi {
    protected function getFullApiUrl($api)
    {
        return 'https://api.github.com/'.$api;
    }
}
```

Create a method called `list` which will list all repos of a user.

```
class RepositoryApi extends Leo108\SDK\AbstractApi {
    public function list($username)
    {
        return $this->apiGet('users/'.$username.'/repos');
    }
}
```

Create a class extends `Leo108\SDK\SDK`, implement the `getApiMap` method.

```
class GithubSDK extends Leo108\SDK\SDK {
    protected function getApiMap()
    {
        return [
            'repository' => RepositoryApi::class,
        ];
    }
}
```

All Done. Let's try it out.

```
$sdk  = new GithubSDK();
// $resp is a Psr\Http\Message\ResponseInterface object
$resp = $sdk->repository->list('leo108');
var_dump($resp->getBody()->getContents());
```

## Work with 
