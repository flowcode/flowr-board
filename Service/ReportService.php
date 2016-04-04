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

    /**
     * AccountDataExport() genera el contenido a ser exportado segun vista.
     *
     */
    public function getDataExport($timelogs)
    {
        $data = array();
        $data["header"] =
            array("values" =>
                array(
                    "Id",
                    "Descripcion",
                    "Fecha",
                    "Horas",
                    "Actividad",
                    "Responsable"
                ),
                "styles" => array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'd22729')
                    )
                ));
        $index = 1;
        foreach ($timelogs as $timelog) {
            $data[$index] =
                array("values" =>
                    array(
                        $timelog->getId() ?: " ",
                        $timelog->getDescription() ?: " ",
                        $timelog->getSpentOn() ?: " ",
                        $timelog->getHours() ?: " ",
                        $timelog->getTask()->getTracker()->getName() ?: " ",
                        $timelog->getUser()->getHappyName() ?: " ",
                    ));
            $index++;
        }
        return $data;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}