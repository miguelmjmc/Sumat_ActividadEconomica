<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Taxpayer;

/**
 * TaxpayerBioPaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaxpayerBioPaymentRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByTaxpayer(Taxpayer $taxpayer)
    {
        $db = $this->createQueryBuilder('bp');

        $db->select('bp')
            ->join('bp.taxpayerBankAccount', 'ba')
            ->where('ba.taxpayer = :taxpayer')
            ->setParameter(':taxpayer', $taxpayer);

        return $db->getQuery()->execute();
    }
}