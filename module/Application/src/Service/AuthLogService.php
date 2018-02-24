<?php

namespace Application\Service;

use Application\Model\User;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGatewayInterface;

class AuthLogService
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var TableGatewayInterface
     */
    protected $successTable;

    /**
     * @var TableGatewayInterface
     */
    protected $failureTable;

    public function __construct(array $config, TableGatewayInterface $successTable, TableGatewayInterface $failureTable)
    {
        $this->config       = $config;
        $this->successTable = $successTable;
        $this->failureTable = $failureTable;
    }

    public function getLastSuccess(User $user)
    {
        return $this->successTable->select(function ($select) use ($user) {
                $select->where(['user_id' => $user->getId()])
                    ->order('datetime DESC')
                    ->limit(1)
                    ->offset(1);
            })->current();
    }

    public function deleteOldFailures()
    {
        $blocktime = $this->config['application']['authentication']['blocktime'];

        return $this->failureTable->delete([
                '`datetime` < NOW() - INTERVAL ? SECOND' => $blocktime
        ]);
    }

    public function isIpBlocked($ip)
    {
        $long        = $this->ip2long($ip);
        $blocktime   = $this->config['application']['authentication']['blocktime'];
        $maxfailures = $this->config['application']['authentication']['maxfailures'];

        $where = [
            'ip'                                     => $long,
            'count >= ?'                             => $maxfailures,
            '`datetime` > now() - INTERVAL ? SECOND' => $blocktime
        ];

        $result = $this->failureTable->select(function ($select) use ($where) {
            $select->where($where)->limit(1);
        });

        return (bool) $result->count();
    }

    public function logSuccess(User $user, $ip, $userAgent)
    {
        $long = $this->ip2long($ip);
        $set  = [
            'user_id'    => $user->getId(),
            'ip'         => $long,
            'user_agent' => substr($userAgent, 0, 255)
        ];

        $this->successTable->insert($set);

        $result = $this->successTable->select(function ($select) use ($user) {
            $select->where(['user_id' => $user->getId()])
                ->order('datetime DESC')
                ->limit(1)
                ->offset(50);
        });
        
        if (!$result->count())
            return;
        
        $this->successTable->delete([
            'user_id'  => $user->getId(),
            'datetime <= ?' => $result->current()->getDatetime()
        ]);
    }

    public function logFailure($ip)
    {
        $long   = $this->ip2long($ip);
        $rowset = $this->failureTable->select(['ip' => $long]);

        if ($rowset->count()) {
            $this->failureTable->update([
                'count'    => new Expression('count + 1'),
                'datetime' => new Expression('now()')
                ], ['ip' => $long]);
        } else {
            $this->failureTable->insert([
                'ip'       => $long,
                'count'    => 1,
                'datetime' => new Expression('now()')
            ]);
        }
    }

    protected function ip2long($ip)
    {
        return sprintf('%u', ip2long($ip));
    }

}
