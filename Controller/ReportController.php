<?php

namespace Flower\BoardBundle\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Board\Board;
use Flower\ModelBundle\Entity\Project\Project;
use Flower\CoreBundle\Form\Type\BoardType;
use Doctrine\ORM\QueryBuilder;

/**
 * Report controller.
 *
 * @Route("/report")
 */
class ReportController extends Controller
{
	/**
     * Lists all Report entities.
     *
     * @Route("/", name="report_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $this->get('database_connection');
        $translator = $this->get('translator');
        $dateformat = $translator->trans('fullDateTime');
        $addGroupBy=null;

        /* Addons, agregados para filtrado */
        $groupBy = ["account", "project", "board"];

        /* Select formater */
        $formaterElem = $request->get("addGroupBy");

        /* Date Filter */
        $startDateFilter = $request->get("startDateFilter");
        $endDateFilter = $request->get("endDateFilter");
        $dates = $this->get('board.service.report')->dateFilter($dateformat, $startDateFilter, $endDateFilter);

        /* SQL Builder */
        $value = $this->get('board.service.report')->getSQLQuery($formaterElem, $dates);
        $tableHeader = $value["tableHeader"];
        $sql = $value["sql"];

        $results = $conn->fetchAll($sql);

        $totalHours = array_sum(array_column($results, 'time_log_hours'));

        return array(
            'paginator' => $results,
            'totalHours' => $totalHours,
            'addGroupBy' => $addGroupBy,
            'groupBy' => $groupBy,
            'tableHeader' => $tableHeader,
            'startDateFilter' => isset($filters['startDateFilter'])?$filters['startDateFilter']["value"] : null,
            'endDateFilter' => isset($filters['endDateFilter'])?$filters['endDateFilter']["value"] : null,
        );
    }

    private function addFilter($qb, $filter, $field)
    {
        if($filter && count($filter) > 0){    
            if( implode(",", $filter) != ""){
                $filterAux = array();
                $nullFilter = "";
                foreach ($filter as $element) {
                    if($element == "-1"){
                        $nullFilter = " OR  (".$field." is NULL)";
                    }else{
                        $filterAux[] = $element;
                    }
                }
                if(count($filterAux) > 0){
                    $qb->andWhere(" ( ".$field." in (:".str_replace(".","_",$field)."_param) ".$nullFilter." )")->setParameter(str_replace(".","_",$field)."_param", $filterAux);
                }else{
                    $qb->andWhere(" ( 1 = 2 ".$nullFilter." )");
                }
            }
        }
    }
}