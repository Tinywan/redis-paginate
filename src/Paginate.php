<?php
/**
 * @desc Paginate.php 描述信息
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/5/6 17:25
 */
declare(strict_types=1);


namespace Tinywan\Redis;


class Paginate
{
    const CHANNEL_ONLINE_USER_LIST = 'channel_online_user_list:';

    protected $redis;

    public function __construct()
    {
        $this->redis = new \Redis();
    }

    /**
     * @desc: 添加
     * @param string $channelId
     * @param string $clientId
     * @param array $data
     * @author Tinywan(ShaoBo Wan)
     */
    public function insert(string $channelId, string $clientId, array $data)
    {
        $pipe = $this->redis->multi(\Redis::PIPELINE);
        $pipe->zAdd(self::CHANNEL_ONLINE_USER_LIST . $channelId, time(), $clientId);
        $pipe->hMSet($channelId  . ':' . self::CHANNEL_ONLINE_USER_LIST . $clientId, $data);
        $pipe->exec();
    }

    /**
     * @desc: page 描述
     * @param string $channelId
     * @param int $page 当前页数
     * @param int $perPage 每页多少条
     * @param array $hashKeys 指定字段查询
     * @param bool $reverse 是否反转
     * @return array
     * @author Tinywan(ShaoBo Wan)
     */
    public function page(string $channelId, int $page, int $perPage,array $hashKeys = [],bool $reverse = false): array
    {
        $zKey = self::CHANNEL_ONLINE_USER_LIST . $channelId;
        $start = ($page - 1) * $perPage;
        $end = ($start + $perPage) - 1;
        if ($reverse) {
            $rangeList = $this->redis->zRevRange($zKey, $start, $end);
        } else {
            $rangeList = $this->redis->zRange($zKey, $start, $end);
        }

        $count = $this->redis->zcard($zKey);
        $pageCount = ceil($count / $perPage);
        $pageList = [];

        if ($rangeList) {
            foreach ($rangeList as $clientId) {
                $_key = $channelId . ':' . self::CHANNEL_ONLINE_USER_LIST . $clientId;
                if (!empty($hashKeys)) {
                    $pageList[$clientId] = $this->redis->hMGet($_key, $hashKeys);
                } else {
                    $pageList[$clientId] = $this->redis->hGetAll($_key);
                }
            }
        }
        return [
            'page' => $page, //当前页数
            'per_page' => $perPage, // 每页多少条
            'count' => $count, // 记录总数
            'page_count' => $pageCount, // 总页数
            'data' => $pageList, // 需求数据
        ];
    }

    /**
     * @desc: 获取单条记录
     * @param string $channelId
     * @param string $clientId
     * @return array
     * @author Tinywan(ShaoBo Wan)
     */
    public function detail(string $channelId, string $clientId): array
    {
        return$this->redis->hGetAll($channelId . ':' . self::CHANNEL_ONLINE_USER_LIST . $clientId);
    }

    /**
     * @desc: 移除
     * @param string $channelId
     * @param string $clientId
     * @author Tinywan(ShaoBo Wan)
     */
    public function remove(string $channelId, string $clientId)
    {
        $pipe = $this->redis->multi(\Redis::PIPELINE);
        $pipe->zRem(self::CHANNEL_ONLINE_USER_LIST . $channelId, $clientId);
        $pipe->del($channelId  . ':' . self::CHANNEL_ONLINE_USER_LIST . $clientId);
        $pipe->exec();
    }
}