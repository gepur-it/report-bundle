<?php
/**
 * Created by PhpStorm.
 * User: marina mileva m934222258@gmail.com
 * Date: 18.01.18
 * Time: 15:12
 */

namespace GepurIt\ReportBundle\Repository;

use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use GepurIt\ReportBundle\Document\AbstractCreateReportCommand;
use GepurIt\ReportBundle\ReportType\ReportTypeCommandRepositoryInterface;

/**
 * Class BaseReportCommandRepository
 * @package ReportBundle\ReportType
 */
class BaseReportCommandRepository extends ServiceDocumentRepository implements ReportTypeCommandRepositoryInterface
{
    /**
     * BaseReportCommandRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractCreateReportCommand::class);
    }

    /**
     * @param array $fields
     * @param int $limit
     * @param int $skip
     * @return AbstractCreateReportCommand[]
     */
    public function findForType(array $fields, int $limit, int $skip): array
    {
        $query = $this->createQueryBuilder()
            ->select($fields)
            ->limit($limit)
            ->skip($skip)
            ->sort('createdAt', -1)
            ->getQuery();

        return $query->toArray();
    }

    /**
     * @return int
     * @throws MongoDBException
     */
    public function count(): int
    {
        return $this->createQueryBuilder()
            ->count()
            ->getQuery()
            ->execute();
    }
}
