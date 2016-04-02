<?php

namespace Flower\BoardBundle\Controller;

use DateTime;
use DoctrineExtensions\Query\Mysql\Date;
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
 * @Route("/timelog/report")
 */
class ReportController extends Controller
{
    /**
     * Lists all Report entities.
     *
     * @Route("/", name="timelog_report_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $todayFrom = new DateTime('first day of this month');
        $todayTo = new DateTime();

        /* dates */
        $fromDate = new DateTime($request->get("from_date", $todayFrom->format('Y-m-d')));
        $toDate = new DateTime($request->get("to_date", $todayTo->format('Y-m-d')));

        /* filters */
        $filters = array(
            "project_id" => $request->get("project_id"),
            "account_id" => $request->get("account_id"),
            "from_date" => $fromDate,
            "to_date" => $toDate,
        );

        $em = $this->getDoctrine()->getManager();

        $totalHours = $em->getRepository('FlowerModelBundle:Board\TimeLog')->getAllSpentFilter($filters, $fromDate, $toDate);

        $qb = $em->getRepository('FlowerModelBundle:Board\TimeLog')->getAllQB($filters, $fromDate, $toDate);

        $paginator = $this->get('knp_paginator')->paginate($qb, $request->get('page', 1), 20);

        $projects = $em->getRepository('FlowerModelBundle:Project\Project')->findAllActive();
        $accounts = $em->getRepository('FlowerModelBundle:Clients\Account')->findAll();

        return array(
            'paginator' => $paginator,
            'filters' => $filters,
            'totalHours' => $totalHours,
            'projects' => $projects,
            'accounts' => $accounts,
        );
    }
}