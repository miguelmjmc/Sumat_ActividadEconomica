<?php

namespace AppBundle\Repository;

/**
 * TaxReturnRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaxReturnRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllDeclared()
    {
        $db = $this
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.declaredAt IS NOT NULL');

        return $db->getQuery()->getResult();
    }
}
