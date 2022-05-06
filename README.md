[![Latest Stable Version](http://poser.pugx.org/tinywan/redis-paginate/v)](https://packagist.org/packages/tinywan/meilisearch)
[![Total Downloads](http://poser.pugx.org/tinywan/redis-paginate/downloads)](https://packagist.org/packages/tinywan/meilisearch)
[![Latest Unstable Version](http://poser.pugx.org/tinywan/redis-paginate/v/unstable)](https://packagist.org/packages/tinywan/meilisearch)
[![License](http://poser.pugx.org/tinywan/redis-paginate/license)](https://packagist.org/packages/tinywan/meilisearch)
[![PHP Version Require](http://poser.pugx.org/tinywan/redis-paginate/require/php)](https://packagist.org/packages/tinywan/meilisearch)

## 安装

```sh
composer require tinywan/redis-paginate
```

## 使用

### 添加

```php
$paginate = new Tinywan\Redis\Paginate();
$paginate->insert('2008','2022',['name'=>'tinywan']);
```

### 分页查询

```php
$paginate = new Tinywan\Redis\Paginate();
$paginate->page('2008',1,10);
```