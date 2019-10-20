<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use PDO;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * Function to obtain user's invoices
     */
    public function getMyInvoices($params, $filters, $user){

        $em = $this->getEntityManager();
        $columns = array(
            0 => 'created_date',
            1 => 'total',
            2 => 'name',
            3 => 'address',
            4 => 'paid',
            5 => 'payment_date',
        );

        $sqlTot = $sqlRec = "";
        $sql = "SELECT  i.id,
                        i.created_date, 
                        i.total, 
                        CONCAT(i.name, ' ', i.lastname) as name, 
                        CONCAT(i.address, ', ', i.city, ', ',i.zip_code, ', ', i.country) as address,
                        i.is_paid as paid,
                        i.payment_date
                   FROM invoice i
                ";
        
        $where = " WHERE i.user_id= '".$user->getId()."'";
        if(count($filters) > 0){
            if(isset($filters['dates'])){
                if (!empty($filters['dates'])) {
                    $where .= !empty(explode(" - ",$filters['dates'])[0]) ? " AND (i.created_date >= '" . explode(" - ",$filters['dates'])[0] . " 00:00:00')" : "";
                }
                if (!empty($filters['dates'])) {
                    $where .= !empty(explode(" - ",$filters['dates'])[1]) ? " AND (i.created_date <= '" . explode(" - ",$filters['dates'])[1] . " 23:59:59')" : "";
                }
            }
            if(isset($filters['paid'])){
                $where .= " AND i.is_paid = ".$filters['paid'];
            }
        }
        $sqlTot .= $sql;
        $sqlRec .= $sql;
        
        if(isset($where) && $where != '') {
            $sqlTot .= $where;
            $sqlRec .= $where;
        }
        
        if(isset($params) && !empty($params) && isset($params['order'])){
            $sqlRec .=  " GROUP BY i.id ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";
        }else{
            $sqlRec .= " GROUP BY i.id ";
        }
        
        $sqlTot .= " GROUP BY i.id ";

        $queryTot = $this->getEntityManager()->getConnection()->executeQuery($sqlTot)->rowCount();
        $queryRecords = $this->getEntityManager()->getConnection()->executeQuery($sqlRec)->fetchAll(\PDO::FETCH_ASSOC);
       
        $json_data = array(
            "recordsTotal"    => intval( $queryTot ),
            "recordsFiltered" => intval( $queryTot ),
            "data"            => $queryRecords,   // total data array
        );
        return $json_data;
    
   }
}
