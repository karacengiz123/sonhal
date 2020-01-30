<?php

namespace App\Datatables;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\Calls;
use App\Entity\Evaluation;
use App\Entity\User;
use App\Entity\UserProfile;
use PhpParser\Builder\Class_;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Filter\Select2Filter;
use Sg\DatatablesBundle\Datatable\Style;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Filter\TextFilter;

/**
 * Class InboundCallDatatable
 *
 * @package App\Datatables
 */
class InboundCallDatatable extends AbstractDatatable
{
    private $agents;
    private $queues;
    private $completeTranslate = ["" => "" , 'COMPLETECALLER' => "Arayan" , "COMPLETEAGENT" => 'Temsilci'];


    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {

        $formatter = function ($row) {
            $row['user']['id'] =$row['user']['id']?$this->agents[$row['user']['id']]:'';
            $row['queue'] = $row['queue']?$this->queues[$row['queue']]:"";

//            $row['dtExten']=$this->trDate('j F Y H:i:s',$row['dtExten']->format('d-m-Y H:i:s'));

            if($row['queueCallId']==null)
            {
                $row['queueCallId']=$row['callId'].'+'.$row['idx'];
//                $row['aktiviteId']=$row['callId'];
            }
            else
            {
                $row['queueCallId']=$row['queueCallId'].'+'.$row['idx'];
//                $row['aktiviteId']=$row['queueCallId'];
            }

            $row['whoComplete'] = $this->completeTranslate[$row['whoCompleted']];
            $userfield=$row['userfield'];
            $audioId=$row['idx'];
            $row['dtExten']=  $row['dtExten']?$this->trDate('j F Y H:i:s',$row['dtExten']->format('Y-m-d H:i:s')):'';
            $row['callPlay'] = "<button class='btn btn-info btn-xs callPlay'  style='font-size:19px;'  data-toggle=\"modal\" data-target=\"#record\" data-id='".$userfield."' data-audioUrl='".$audioId."' onclick='callPlayClick($audioId)'>Dinle</button>";
            $row['history'] = "<button class='btn btn-warning btn-xs history'  style='font-size:19px;'  data-toggle=\"modal\" data-target=\"#historyModal\" data-id='`".$row['queueCallId']."`'  onclick=\"historyCheck(`".$row['queueCallId']."`)\"><i class='fas fa-history'></i></button>";
            return $row;
        };
        return $formatter;
    }

    /**
     * {@inheritdoc}
     * @throws
     */
    public function buildDatatable(array $options = array())
    {

        $this->getAgentList();
        $this->getQueueList();

        $this->language->set(array(
            'cdn_language_by_locale' => true,
            'language'=>'tr'
        ));

        $this->ajax->set(array());
        $this->options->set(array(
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order_cells_top' => true,
            'dom' => 'Bfrtip',
            'state_duration' => 200*200*200,

        ));

        $this->features->set(array('state_save' => true));

        $this->extensions->set(array(

            'responsive' => true,
        ));


        $this->columnBuilder
            ->add('idx', Column::class, array(
                'title' => 'Id',
                'visible' => false,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('dtExten', Column::class, array(
                'title' => 'Başlangıç Zamanı',
                'filter' => array(DateRangeFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
                'width'=>'100px!important'
            ))
            ->add('user.id',Column::class,array(
                'title' => 'Temsilci Adı',
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->agents,
                    'search_type' => 'eq',
                    'cancel_button' => true,


                )),
                'width'=>'100px'
            ))
            ->add('callId', Column::class, array(
                'title' => 'call',
                'visible' => false,
            ))
            ->add('clid', Column::class, array(
                'title' => 'Arayan',
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button'=>true
                    ),
                ),
                'width'=>'110px'


            ))
            ->add('exten', Column::class, array(
                'title' => 'Aranan',
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button'=>true
                    ),
                ),
                'width'=>'110px'


            ))
            ->add('durExten', Column::class, array(
                'title' => 'Süre',
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                        'cancel_button'=>true
                    ),
                ),
                'width'=>'80px'

            ))
            ->add('queue', Column::class, array(
                'title' => 'Kuyruk',
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->queues,
                    'search_type' => 'eq',
                    'cancel_button' => true,
                )),
                'width'=>'80px!important'
            ))
            ->add('whoComplete', VirtualColumn::class, array(
                'title' => 'Kapatan',
                'filter' => array(TextFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('whoCompleted', Column::class, array(
                'title' => 'Kapatan',
                'visible' => false,
            ))->add('userfield', Column::class, array(
                'title' => 'Kapatan',
                'visible' => false,
            ))
            ->add('queueCallId', Column::class, array(
                'title' => 'callid',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'cancel_button' => true,
                        'search_type' => 'eq',
                    ),
                ),
                'class_name'=>'filter_callID_twig'
            ))
            ->add('callType', Column::class, array(
                'title' => 'Arama Tipi',
                'filter' => array(TextFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
                'width'=>'80px'
            ))
            ->add(null, ActionColumn::class, array(
                'title' => 'İşlemler',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'quality_new',
                        'route_parameters' => array(
                            'callid' => 'queueCallId',
//                            'idx'=>'idx'
                        ),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'label' => 'Değerlendir',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Show',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button',
                        ),
                    ),
                ),
                'width'=>'70px'
            ))
            ->add('callPlay',VirtualColumn::class,array(
                'title'=>'Kayıt',
                'visible'=>true
            ))
            ->add('history',VirtualColumn::class,array(
                'title'=>'Geçmiş',
                'visible'=>true
            ));
    }

    public function getQueueList()
    {
        $options = [];
        $queues = $this->getEntityManager()->getRepository(Queues::class)->findAll();

        foreach ($queues as $queue) {
            $options[$queue->getQueue()] = $queue->getDescription();
        }

        $this->queues = $options;

    }

    public function getAgentList()
    {
        $agents = $this->getEntityManager()->getRepository(UserProfile::class)->findAll();
        $options = [];
        foreach ($agents as $agent) {
            $options[$agent->getUser()->getId()] = $agent->getFirstName() . " " . $agent->getLastName();
        }
        $this->agents = $options;

    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Calls::class;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'calls_datatable';
    }


    public function trDate($format, $datetime = 'now'){
        $z = date("$format", strtotime($datetime));
        $gun_dizi = array(
            'Monday'    => 'Pazartesi',
            'Tuesday'   => 'Salı',
            'Wednesday' => 'Çarşamba',
            'Thursday'  => 'Perşembe',
            'Friday'    => 'Cuma',
            'Saturday'  => 'Cumartesi',
            'Sunday'    => 'Pazar',
            'January'   => 'Ocak',
            'February'  => 'Şubat',
            'March'     => 'Mart',
            'April'     => 'Nisan',
            'May'       => 'Mayıs',
            'June'      => 'Haziran',
            'July'      => 'Temmuz',
            'August'    => 'Ağustos',
            'September' => 'Eylül',
            'October'   => 'Ekim',
            'November'  => 'Kasım',
            'December'  => 'Aralık',
            'Mon'       => 'Pts',
            'Tue'       => 'Sal',
            'Wed'       => 'Çar',
            'Thu'       => 'Per',
            'Fri'       => 'Cum',
            'Sat'       => 'Cts',
            'Sun'       => 'Paz',
            'Jan'       => 'Oca',
            'Feb'       => 'Şub',
            'Mar'       => 'Mar',
            'Apr'       => 'Nis',
            'Jun'       => 'Haz',
            'Jul'       => 'Tem',
            'Aug'       => 'Ağu',
            'Sep'       => 'Eyl',
            'Oct'       => 'Eki',
            'Nov'       => 'Kas',
            'Dec'       => 'Ara',
        );
        foreach($gun_dizi as $en => $tr){
            $z = str_replace($en, $tr, $z);
        }
        if(strpos($z, 'Mayıs') !== false && strpos($format, 'F') === false) $z = str_replace('Mayıs', 'May', $z);
        return $z;
    }

}