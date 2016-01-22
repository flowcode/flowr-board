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

        /* Addons, agregados para filtrado */
        $groupBy = ["account", "project", "board"];

        /* Date Filter */
        $value = null;
        $startDateFilter = $request->get("startDateFilter");
        $startDateSQL = "";
        if($startDateFilter && $startDateFilter != ""){
            $startDateFilter = \DateTime::createFromFormat($dateformat, $startDateFilter);
            $value = $startDateFilter->format('Y-m-d H:i:s');
            $startDateSQL = 'AND tl.spent_on >= "'. $value. '" ';
        }
        $endDateFilter = $request->get("endDateFilter");
        $endDateSQL = "";
        if($endDateFilter && $endDateFilter != ""){
            $endDateFilter = \DateTime::createFromFormat($dateformat, $endDateFilter);
            $value = $endDateFilter->format('Y-m-d H:i:s');
            $endDateSQL = 'AND tl.spent_on <= "'. $value. '" ';
        }

        $addGroupBy=null;

        $sql = "";
        $sql .= 'SELECT a.name as "account", p.name as "project", b.name as "board", t.id as "task_id", t.name as "task_name", t.created as "task_created", tl.hours as "time_log_hours",tl.created_on as "time_log_created"';
        $sql .= 'FROM  ';
        $sql .= 'time_log tl,  ';
        $sql .= 'task t, ';
        $sql .= 'board b, ';
        $sql .= 'project p, ';
        $sql .= 'account a, ';
        $sql .= 'project_boards pb ';
        $sql .= 'WHERE 1=1 ';
        $sql .= 'AND tl.task_id = t.id ';
        $sql .= 'AND t.board_id = b.id ';
        $sql .= 'AND b.id = pb.board_id ';
        $sql .= 'AND p.id = pb.project_id ';
        $sql .= 'AND a.id = p.account_id ';
        $sql .= $startDateSQL;
        $sql .= $endDateSQL;
        //echo $sql;

        /* Select formater */
        $formaterElem = $request->get("addGroupBy");
        /* Solo se agrega cuando se elige por lo menos una opcion no nula. */
        if(count($formaterElem) > 1 || $formaterElem[0] != ""){
            /* Filtro por si se agrego la opcion vacio. */
            $var = array_filter($request->get("addGroupBy"));
            $var = implode(', ', $var);
            $sql .= "GROUP BY ". $var;
        }

        $results = $conn->fetchAll($sql);

        $totalHours = array_sum(array_column($results, 'time_log_hours'));

        return array(
            'paginator' => $results,
            'totalHours' => $totalHours,
            'addGroupBy' => $addGroupBy,
            'groupBy' => $groupBy,
            'startDateFilter' => isset($filters['startDateFilter'])?$filters['startDateFilter']["value"] : null,
            'endDateFilter' => isset($filters['endDateFilter'])?$filters['endDateFilter']["value"] : null,
        );
    }

    private function getSelect($groupBy)
    {
        $select = "";

        foreach ($groupBy as $value) {
            if($value == "account"){

            } elseif($value == "project"){
            }
        }
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