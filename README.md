# 安裝
需要使用composer，[安裝composer](https://getcomposer.org/download/)

如果是應用在項目當中的話找到根目錄，需要和 `composer.json`同級

```
composer require qqjameqq1/php_redis_lbs
```

## 配置
如果不是 `laravel` 框架的話，需要修改配置文件 `src/config/config.php`
```
  'geoset_name' => 'LBS_set',
    'radium_option' => [
        'WITHDIST' => true,
        'SORT' => 'asc',
        'WITHHASH' => false,
    ],
    'redis_connection' => [
        'host'     => '127.0.0.1',      //連接地址
        'port'     => 6379,             //端口
        'database' => 1,                //庫索引
        'password' => null,             //密碼
    ],

```
***但是***，如果是在 `vendor` 文件夾下修改就不能將它從版本庫中移除了，所以也可以按照以上的格式寫一個數組初始化的時候添加進去比如:
```
$config = [
              'geoset_name' => 'LBS_set',         //集合名
              'radium_option' => [                //搜尋附近的人的時候定義的一些參數
                  'WITHDIST' => true,
                  'SORT' => 'asc',
                  'WITHHASH' => false,
              ],
              'redis_connection' => [
                  'host'     => '127.0.0.1',      //連接地址
                  'port'     => 6379,             //端口
                  'database' => 1,                //庫索引
                  'password' => null,             //密碼
              ],
          ];
 $lbs = new \LBS\Services\LBSService($config);
```

如果是 `laravel` 框架下，需要編輯 `config/app.php`
```
 'providers' => [
    ...
     \LBS\Provider\RedisLbsProvider::class,
    ...
  ],

 //如果需要facade模式的話也可以開一下
  'aliases' => [
    ...
    'LBSServer' => \LBS\Facade\LBSServer::class,
    ...
  ]
```

然後執行
```
php artisan vendor:publish
```
將生成 `config/redis_lbs.php` 配置文件，配置文件中的
```
//是否應用在laravel當中
'is_laravel' => false,
//使用laravel的redis版本
'laravel_redis' => 'default',
```
當 `is_laravel => true` 的時候， `laravel_redis => 'default'` 將調用 `config/database.php`下的redis相應的配置


有以下三種使用方式
```
1> $lbs = new \LBS\Services\LBSServer();

2> public function __construct(LBSInterface $LBS)
       {
           $list = $LBS->list($LBS->geoset_name);

           dd($list);
       }
   }
3> $search2 = \LBSServer::searchByMembers('fesco',500,'m');

```

#基本操作

## 初始化
```
require_once __DIR__.'/vendor/autoload.php';
$lbs = new \LBS\Services\LBSService();
```

## 添加
```
$add_params = [
    [
        'name' => 'yabao_road',
        'long' => '116.43620200729366',
        'lat' => '39.916880160714435'
    ],
    [
        'name' => 'jianguomen',
        'long' => '116.4356870231628',
        'lat' => '39.908560377800676'
    ],
    [
        'name' => 'chaoyangmen',
        'long' => '116.4345336732864',
        'lat' => '39.924466658329585'
    ],
    [
        'name' => 'galaxy_soho',
        'long' => '116.4335788068771',
        'lat' => '39.921372916981106'
    ],
    [
        'name' => 'cofco',
        'long' => '116.43564410781856',
        'lat' => '39.92024564137184'
    ],
    [
        'name' => 'fesco',
        'long' => '116.435182767868',
        'lat' => '39.91811857809279'
    ],


];
/**
 * 在集合中新加一個坐標
 * @param array $params
 *  結構是 ['name'=>'xxx','long'=>'1.2321','lat'=>'1.3112']或者[['name'=>'xxx','long'=>'1.2321','lat'=>'1.3112']]
 * @param null $key
 * @return int
 */
$res = $lbs->add($add_params);

返回
int 6
```

## 刪除
```
/**
 * 刪除集合中指定元素
 * @param $name
 * @param null $key  默認存在集合，可以指定
 * @return int
 */
$res = $lbs->del('gao1');

返回
int 0 或 1


如果是指定的集合名就
$res = $lbs->del('gao1','set-name');
```

## 用坐標查詢附近的單位
```
/**
 * 查詢範圍內元素，如果不轉 key就用默認的
 * @param $long     經度
 * @param $lat      緯度
 * @param $radius   範圍
 * @param $unit     單位  (僅支持 m,km,ft,mi)
 * @param null $key 集合名
 * @return mixed
 */
$search = $lbs->search('116.435182767868','39.91811857809279',500,'m');

返回數組
array:4 [▼
  0 => array:2 [▼
    "name" => "fesco"
    "dist" => "0.1250"
  ]
  1 => array:2 [▼
    "name" => "yabao_road"
    "dist" => "162.8454"
  ]
  2 => array:2 [▼
    "name" => "cofco"
    "dist" => "239.7758"
  ]
  3 => array:2 [▼
    "name" => "galaxy_soho"
    "dist" => "386.9165"
  ]
]
```

## 根據已有的位置查詢
```
/**
 * 根據集合中的元素查詢範圍內元素，如果不轉 key就用默認的
 * @param $name         集合中的元素名
 * @param $radius       範圍
 * @param $unit         單位
 * @param null $key     集合名
 * @return mixed
 */
$search = $lbs->->searchByMembers('fesco',500,'m');

返回數組
array:4 [▼
  0 => array:2 [▼
    "name" => "fesco"
    "dist" => "0.1250"
  ]
  1 => array:2 [▼
    "name" => "yabao_road"
    "dist" => "162.8454"
  ]
  2 => array:2 [▼
    "name" => "cofco"
    "dist" => "239.7758"
  ]
  3 => array:2 [▼
    "name" => "galaxy_soho"
    "dist" => "386.9165"
  ]
]
```

## 列出集合的所有值（其實就是 zrange)
```
/**
 * 列出集合中的內容
 * @param $key          集合的key
 * @param int $start    起始位置
 * @param int $end      結束位置 -1 為直到末尾
 * @return array
 */
$list = $lbs->list($test->geoset_name,2,-1);

返回數組
array:6 [▼
  0 => "jianguomen"
  1 => "yabao_road"
  2 => "fesco"
  3 => "cofco"
  4 => "galaxy_soho"
  5 => "chaoyangmen"
]
```
