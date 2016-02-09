<?php

namespace Flower\BoardBundle\Service;


use Flower\ModelBundle\Entity\Board\TaskAttachment;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReportService implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function getSQLQuery($formaterElem, $dates)
    {

        $value = $this->selectStatement($formaterElem);
        $selectSQL = $value["select"];
        $tableHeader = $value["tableHeader"];

        $addOns = false;
        $sumHours = ' tl.hours as "time_log_hours" ';
        /* Una opcion no nula en el agregado. */
        if(count($formaterElem) > 1 || $formaterElem[0] != "") {
            $sumHours = ' SUM(tl.hours) as "time_log_hours" ';
            $addOns = true;
        }

        $sql = "";
        $sql .= $selectSQL;
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
        $sql .= $dates['startDateSQL'];
        $sql .= $dates['endDateSQL'];

        
        /* Solo se agrega cuando se elige por lo menos una opcion no nula. */
        if($addOns) {
            /* Filtro por si se agrego la opcion vacio. */
            $var = array_filter($formaterElem);
            $var = implode(', ', $var);
            $sql .= "GROUP BY ". $var;
        }

        return array("sql" => $sql, "tableHeader" => $tableHeader);
    }

    public function selectStatement($groupBy)
    {
        $select = 'SELECT a.name as "account", ';
        $project = ' p.name as "project", ';
        $board = ' b.name as "board", ';
        $rest = ' t.id as "task_id", t.name as "task_name", t.created as "task_created", tl.hours as "time_log_hours",tl.created_on as "time_log_created" ';
        $sumHours = ' SUM(tl.hours) as "time_log_hours" ';
        
        $tableHeader = array("account");
        if((count($groupBy) == 1 && $groupBy[0] == null) || !$groupBy) {
            $select .= $project;
            $select .= $board;
            $select .= $rest;
            array_push($tableHeader, "project", "board", "task_id", "task_name", "task_created", "time_log_hours", "time_log_created");
        } else {
            if(in_array("board", $groupBy)) {
                $select .= $project;
                $select .= $board;
                array_push($tableHeader, "project", "board");
            } elseif(in_array("project", $groupBy)) {
                $select .= $project;
                array_push($tableHeader, "project");
            }
            array_push($tableHeader, "time_log_hours");
            $select .= $sumHours;
        }
        $value = array("select" => $select, "tableHeader" => $tableHeader);
        return $value;
    }

    public function dateFilter($dateformat, $startDateFilter, $endDateFilter)
    {
        $value = null;
        $startDateSQL = "";
        if($startDateFilter && $startDateFilter != ""){
            $startDateFilter = \DateTime::createFromFormat($dateformat, $startDateFilter);
            $value = $startDateFilter->format('Y-m-d H:i:s');
            $startDateSQL = 'AND tl.spent_on >= "'. $value. '" ';
        }
        $endDateSQL = "";
        if($endDateFilter && $endDateFilter != ""){
            $endDateFilter = \DateTime::createFromFormat($dateformat, $endDateFilter);
            $value = $endDateFilter->format('Y-m-d H:i:s');
            $endDateSQL = 'AND tl.spent_on <= "'. $value. '" ';
        }
        return $datesFilter = array("startDateSQL" => $startDateSQL, "endDateSQL" => $endDateSQL);
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}