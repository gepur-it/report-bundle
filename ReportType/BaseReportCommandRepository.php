<?php
/**
 * Created by PhpStorm.
 * User: marina mileva m934222258@gmail.com
 * Date: 18.01.18
 * Time: 15:12
 */

namespace GepurIt\ReportBundle\ReportType;

use Doctrine\ODM\MongoDB\DocumentRepository;
use GepurIt\ReportBundle\CreateCommand\AbstractCreateReportCommand;

/**
 * Class BaseReportCommandRepository
 * @package ReportBundle\ReportType
 */
class BaseReportCommandRepository extends DocumentRepository implements ReportTypeCommandRepositoryInterface
{
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
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function count(): int
    {
        return $this->createQueryBuilder()
            ->count()
            ->getQuery()
            ->execute();
    }
}