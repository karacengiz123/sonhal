<?php

namespace App\Datatables;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\Calls;
use App\Entity\Evaluation;
use App\Entity\EvaluationReasonType;
use App\Entity\EvaluationSource;
use App\Entity\EvaluationStatus;
use App\Entity\FormTemplate;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use function GuzzleHttp\Promise\exception_for;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Editable\SelectEditable;
use Sg\DatatablesBundle\Datatable\Editable\TextEditable;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Filter\Select2Filter;
use Sg\DatatablesBundle\Datatable\Style;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\BooleanColumn;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\MultiselectColumn;
//use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\ImageColumn;
use Sg\DatatablesBundle\Datatable\Filter\TextFilter;
use Sg\DatatablesBundle\Datatable\Filter\NumberFilter;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Symfony\Bundle\FrameworkBundle\Tests\CacheWarmer\testRouterInterfaceWithoutWarmebleInterface;

/**
 * Class InboundCallDatatable
 *
 * @package App\Datatables
 */
class EvaluationListDatatable extends AbstractDatatable
{
    private $agents;
    private $queues;
    private $source;
    private $status;


    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {

        $formatter = function ($row) {
            $row['userId']=$row['user']["id"];
            $agentId=$row['userId'];
            $row['user']["id"] = $this->agents[$row['user']["id"]];
            $historyId=$row["id"];
            $agentName=$row['user']["id"];

//            $ids=explode('+',$row['sourceDestID']);
//            if(isset($ids[1]))
//            {
//                $row['phoneNumber']=$this->getEntityManager()->getRepository(Calls::class)->findOneBy(['idx'=>$ids])->getClid();
//            }
//            else
//            {
//                $row['phoneNumber']="";
//            }

            $row['callDate']=$this->turkcetarih_formati('j F Y H:i:s',$row['callDate']->format('Y-m-d H:i:s'));
            $row['createdAt']=$this->turkcetarih_formati('j F Y H:i:s',$row['createdAt']->format('Y-m-d H:i:s'));

            $userRoles = $this->getUser()->getRoles();
            if (array_search("ROLE_KALITE_ADMIN",$userRoles) != false){
                $row['evaluative']['id']=$this->agents[$row['evaluative']['id']];
            }
            elseif (array_search("ROLE_KALITE_AGENT",$userRoles) != false){
                $row['evaluative']['id']=$this->agents[$row['evaluative']['id']];

            }
            elseif (array_search("ROLE_TAKIM_LIDERI",$userRoles) != false){
                if($row['evaluative']['id']==$this->getUser()->getId())
                {
                    $row['evaluative']['id']=$this->agents[$row['evaluative']['id']];
                }
                else{
                    $row['evaluative']['id']="****** ******";
                }
            }
            elseif (array_search("ROLE_SUPERVISOR",$userRoles) != false){

                if($row['evaluative']['id']==$this->getUser()->getId())
                {
                    $row['evaluative']['id']=$this->agents[$row['evaluative']['id']];
                }
                else{
                    $row['evaluative']['id']="****** ******";
                }
            }
            $row['history'] = "<button class='btn btn-success btn-xs' id='historyPro$historyId' style='font-size:17px;'
                data-toggle=\"modal\" data-target=\"#historyModal\" data-id='$agentId' data-name='$agentName' onclick='historyCheck($historyId)'>Geçmiş</button>";
            return $row;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function buildDatatable(array $options = array())
    {
        $this->getAgentList();
        $this->getQueueList();
        $this->getSourceList();
        $this->getStatusList();
        $this->getReasonType();
        $this->getFormTemplate();

//        $this->getQualityTeamList();

        $this->language->set(array(
            'cdn_language_by_locale' => true,
//            'language_by_locale'=>'Europe/Istanbul'
        ));

        $this->ajax->set(array());

        $this->options->set(array(
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order_cells_top' => true,
            'order' => array(array($this->getDefaultOrderCol(), 'asc')),
            'dom' => 'Bfrtip',
        ));

        $this->features->set(array('state_save' => true));

        $this->extensions->set(array(
            'responsive' => true,
        ));


            $this->columnBuilder->add('id', Column::class, array(
                'title' => 'Id',
                'visible' => false,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ));
            $this->columnBuilder->add('source.id', Column::class, array(
                'title' => 'Kaynak',
                'visible' => false,
                'filter' => array(TextFilter::class, array(

                    'search_type' => 'eq',

                )),
            ));
            $this->columnBuilder->add('sourceDestID', Column::class, array(
                'title' => 'kaynakid',
                'visible' => false,
                'filter' => array(TextFilter::class, array(
                    'search_type' => 'eq',
                )),
            ));
            $this->columnBuilder->add('callDate', Column::class, array(
                'title' => 'Çağrı Zamanı',
                'filter' => array(DateRangeFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
                'width'=>'110px'
            ));
            $this->columnBuilder->add('createdAt', Column::class, array(
                    'title' => 'Değerlendirme Tarihi',
                    'filter' => array(DateRangeFilter::class,
                        array(
                            'cancel_button' => true,
    //                        'search_type' => 'eq',
                            
                        ),
                    ),
                    'width'=>'110px'
                ));
            $userRoles = $this->getUser()->getRoles();
            if (array_search("ROLE_KALITE_ADMIN",$userRoles) != false){
                $this->columnBuilder->add('evaluative.id', Column::class, array(
                    'title' => 'Değerlendiren',
                    'visible' => true,

                    'filter' => array(Select2Filter::class, array(
                        'select_options' => array('' => 'Hepsi') + $this->agents,
                        'search_type' => 'eq',
                        'cancel_button' => true
                    )),
                    'width'=>'110px'
                ));
            }
            elseif (array_search("ROLE_KALITE_AGENT",$userRoles) != false){
                $this->columnBuilder->add('evaluative.id', Column::class, array(
                    'title' => 'Değerlendiren',
                    'visible' => true,

                    'filter' => array(Select2Filter::class, array(
                        'select_options' => array('' => 'Hepsi') + $this->agents,
                        'search_type' => 'eq',
                        'cancel_button' => true
                    )),
                    'width'=>'110px'
                ));
            }
            elseif (array_search("ROLE_TAKIM_LIDERI",$userRoles) != false){
                /**
                 * @var User $user
                 */
                $user=$this->getUser();
                $this->columnBuilder->add('evaluative.id', Column::class, array(
                    'title' => 'Değerlendiren',
                    'visible' => true,
                    'filter' => array(Select2Filter::class, array(
                        'select_options' => array('' => 'Hepsi') + [$user->getId()=>$user->__toString()],
                        'search_type' => 'eq',
                        'cancel_button' => true
                    )),
                    'width'=>'110px'
                ));
            }
            elseif (array_search("ROLE_SUPERVISOR",$userRoles) != false){
                /**
                 * @var User $user
                 */
                $user=$this->getUser();
                $this->columnBuilder->add('evaluative.id', Column::class, array(
                    'title' => 'Değerlendiren',
                    'visible' => true,
                    'filter' => array(Select2Filter::class, array(
                        'select_options' => array('' => 'Hepsi') + [$user->getId()=>$user->__toString()],
                        'search_type' => 'eq',
                        'cancel_button' => true
                    )),
                    'width'=>'110px'
                ));
            }
//            elseif(array_search("ROLE_TAKIM_LIDERI",$userRoles)) {
//                $this->columnBuilder->add('evaluative.id', Column::class, array(
//                    'title' => 'Değerlendiren',
//                    'visible' => true,
//
//                    'filter' => array(Select2Filter::class, array(
//                        'select_options' => array('' => 'Hepsi') + $this->agents,
//                        'search_type' => 'eq',
//                        'cancel_button' => true
//                    )),
//                    'width'=>'120px'
//                ));
//            }
            $this->columnBuilder->add('user.id', Column::class, array(
                'title' => 'Değerlendirilen',
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->agents,
                    'search_type' => 'eq',
                    'cancel_button' => true,
                )),
                'width'=>'110px'
            ));
            $this->columnBuilder->add('formTemplate.title', Column::class, array(
                'title' => 'Form Adı',
                'visible' => true,
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->form,
                    'search_type' => 'eq',
                    'cancel_button' => true,
                )),
                'width'=>'150px'
            ));
            $this->columnBuilder->add('source.name', Column::class, array(
                'title' => 'Değerlendirme Tipi',
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->source,
                    'search_type' => 'eq',
                    'cancel_button' => true,
                )),
                'width'=>'70px'
            ));
            $this->columnBuilder->add('evaluationReasonType.name', Column::class, array(
                'title' => 'Form Tipi',
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->reasons,
                    'search_type' => 'eq',
                    'cancel_button' => true,
                )),
                'width'=>'140px'
            ));
            $this->columnBuilder->add('score', Column::class, array(
                'title' => 'Skor',
                'filter' => array(TextFilter::class, array(
                    'search_type' => 'eq',
                    'cancel_button'=>true
                )),
                'width'=>'60px'
            ));
            $this->columnBuilder->add('phoneNumber', Column::class, array(
                'title' => 'Telefon',
                'filter' => array(TextFilter::class, array(
                    'search_type' => 'eq',
                    'cancel_button'=>true
                )),
                'width'=>'110px'
            ));
            $this->columnBuilder->add('sourceDestID', Column::class, array(
                'title' => 'Aktivite ID',
                'filter' => array(TextFilter::class, array(
                    'search_type' => 'eq',
                    'cancel_button'=>true
                )),
                'width'=>'100px'
            ));
            $this->columnBuilder->add('status.name', Column::class, array(
                'title' => 'Durum',
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->status,
                    'search_type' => 'eq',
                    'cancel_button' => true,

                )),
                'width'=>'90px'
            ));
            $this->columnBuilder->add(null,ActionColumn::class, array(
                'title' => 'Detay',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'quality_evalutation',
                        'route_parameters' => array(

                            'evaluationId' => 'id',
                        ),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'label' => 'Detay Gör',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Detay',
                            'class' => 'btn btn-primary btn-xs detailBtn',
                            'role' => 'button',
                        ),
                    ),
                ),
            ));
            $userRoles = $this->getUser()->getRoles();
            if (array_search("ROLE_KALITE_ADMIN",$userRoles) != false){
                $this->columnBuilder->add('history', VirtualColumn::class, array(
                    'title' => 'Geçmiş',
                    'visible' => true,

                ) );
            }
            elseif (array_search("ROLE_KALITE_AGENT",$userRoles) != false){
                $this->columnBuilder->add('history', VirtualColumn::class, array(
                    'title' => 'Geçmiş',
                    'visible' => true,
                ) );
            }
            $this->columnBuilder->add('userId', VirtualColumn::class, array(
                    'title' => 'user',
                    'visible' => false,

                ) );

        }

    public function getQueueList()
    {
        $queues = $this->getEntityManager()->getRepository(Queues::class)->findAll();

        foreach ($queues as $queue) {
            $options[$queue->getQueue()] = $queue->getDescription();
        }

        $this->queues = $options;

    }

    public function getAgentList()
    {
        $agents = $this->getEntityManager()->getRepository(UserProfile::class)->findAll();

        foreach ($agents as $agent) {
            $options[$agent->getUser()->getId()] = $agent->getFirstName() . " " . $agent->getLastName();
        }

        $this->agents = $options;

    }

    public function getSourceList()
    {
        $sources = $this->getEntityManager()->getRepository(EvaluationSource::class)->findAll();

        foreach ($sources as $source) {
            $options[$source->getName()] = $source->getName();
        }

        $this->source = $options;

    }

    public function getStatusList()
    {
        $sources = $this->getEntityManager()->getRepository(EvaluationStatus::class)->findAll();
        $options = [];
        foreach ($sources as $source) {
            $options[$source->getName()] = $source->getName();
        }

        $this->status = $options;

    }

    public function getReasonType()
    {
        $reasons = $this->getEntityManager()->getRepository(EvaluationReasonType::class)->findAll();
        $options = [];
        foreach ($reasons as $reason) {
            $options[$reason->getName()] = $reason->getName();
        }

        $this->reasons = $options;

    }

    public function getFormTemplate()
    {
        $forms = $this->getEntityManager()->getRepository(FormTemplate::class)->findAll();
        $options = [];
        foreach ($forms as $form) {
            $options[$form->getTitle()] = $form->getTitle();
        }

        $this->form = $options;

    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Evaluation::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'evalist_datatable';
    }

    /**
     * Get User.
     *
     * @return mixed|null
     */
    private function getUser()
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->securityToken->getToken()->getUser();
        } else {
            return null;
        }
    }

    /**
     * Is admin.
     *
     * @return bool
     */
    private function isAdmin()
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    /**
     * Get default order col.
     *
     * @return int
     */
    private function getDefaultOrderCol()
    {
        return true === $this->isAdmin() ? 1 : 0;
    }

   private function turkcetarih_formati($format, $datetime = 'now'){
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


    /**
     * Returns the columns which are to be displayed in a pdf.
     *
     * @return array
     */
    private function getPdfColumns()
    {
        if (true === $this->isAdmin()) {
            return array(
                '1', // id column
                '2', // title column
                '3', // visible column
            );
        } else {
            return array(
                '0', // id column
                '1', // title column
                '2', // visible column
            );
        }
    }
}