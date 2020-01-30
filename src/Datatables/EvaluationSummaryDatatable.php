<?php

namespace App\Datatables;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\Evaluation;
use App\Entity\EvaluationSource;
use App\Entity\EvaluationStatus;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserProfile;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\NoResultException;
use PhpParser\Node\Stmt\Expression;
use r\Queries\Selecting\Filter;
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

/**
 * Class InboundCallDatatable
 *
 * @package App\Datatables
 */
class EvaluationSummaryDatatable extends AbstractDatatable
{

    private $endTime;
    private $startThisMonth;
    private $startPrevMonth;
    private $endPrevMonth;
    private $startPeriod;
    private $startThisYear;
    private $startLastYear;
    private $teams;


    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {
        $this->setDateTimes();

        $evaList = $this->getEntityManager()->getRepository(EvaluationSource::class);


        $formatter = function ($row) {
            $row['month'] = $this->countEvaulations($this->startThisMonth, $this->endTime, $row['id']);
            $row['prevMonth'] = $this->countEvaulations($this->startPrevMonth, $this->endPrevMonth, $row['id']);
            $row['period'] = $this->countEvaulations($this->startPeriod, $this->endTime, $row['id']);
            $row['thisYear'] = $this->countEvaulations($this->startThisYear, $this->endTime, $row['id']);
            $row['lastYear'] = $this->countEvaulations($this->startLastYear, $this->endTime, $row['id']);
            $row['waiting'] = $this->countEvaulations($this->startThisMonth, $this->endTime, $row['id'], true);
            $rowsID = $row['id'];
            $row['changefilter'] = "<button class='btn btn-primary btn-xs' style='font-size:20px;' onclick='changeAgentfilter(" . $rowsID . ")' > Seç</button>";
            $row['checkEva'] = "<button class='btn btn-info btn-xs'  data-toggle=\"modal\" data-target=\"#evaCheckModal\"  style='font-size:20px;' onclick='evaCheck(" . $rowsID . ")' ><i class=\"fab fa-algolia\"></i></button>";

            return $row;
        };

        return $formatter;
    }

    public function countEvaulations($start, $end, $userId, $waiting = false)
    {
        $user = $this->getEntityManager()->find(User::class, $userId);

        try {
            $query = $this->getEntityManager()->getRepository(Evaluation::class)->createQueryBuilder('e')
                ->where('e.createdAt between :start and :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->andWhere('e.user = :user')->setParameter('user', $user)
                ->select('count(DISTINCT e.id)');
            if ($waiting) {
                $query->andWhere('e.status  in(:statuses)')
                    ->setParameter('statuses', $this->getEntityManager()->getRepository(EvaluationStatus::class)->findById([1, 2]));
            }
            $count = $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException $noResultException) {
            $count = 0;
        }
        return $count;
    }

    public function setDateTimes()
    {
        $now = new \DateTime();
        $this->endTime = $now->format('Y-m-d H:i:s');
        $now = new \DateTime();

        $this->startThisMonth = $now->modify('first day of this month')->format('Y-m-d 00:00:00');


        $periodStartMonth = ((ceil(date('m') / 3) - 1) * 3) + 1;
        $period = new \DateTime(Date('Y') . "-" . $periodStartMonth . "-01");

        $this->startPeriod = $period->format('Y-m-d 00:00:00');
        $this->startPrevMonth = $now->modify('-1 month')->modify('first day of this month')->format('Y-m-d 00:00:00');
        $this->endPrevMonth = $now->modify('last day of this month')->format('Y-m-d 23:59:59');
        $this->startThisYear = $now->modify('first day of this year')->format('Y-m-d 00:00:00');
        $now = new \DateTime();
        $this->startLastYear = $now->modify('-1 year')->modify('first day of this month')->format('Y-m-d 00:00:00');

    }

    /**
     * {@inheritdoc}
     * @throws
     */
    public function buildDatatable(array $options = array())
    {

        $this->setDateTimes();
        $this->getTeamList();


        $this->language->set(array(
            'cdn_language_by_locale' => true,
        ));

        $this->ajax->set(array());

        $this->options->set(array(
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order_cells_top' => true,
            'dom' => 'Bfrtip',
            "page_length" => 3,
        ));

        $this->features->set(array('state_save' => true));

        $this->extensions->set(array(
            'responsive' => true,
        ));
        $this->ajax->setUrl('/quality/summary');
        $this->columnBuilder
            ->add('id', Column::class, array(
                'title' => 'Id',
                'visible' => false,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),

            ))
            ->add('username', Column::class, array(
                'title' => 'Kullanıcı Adı',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button'=>true

                    ),
                ), 'width' => '120px'
            ))
            ->add('userProfile.firstName', Column::class, array(
                'title' => 'Adı',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button'=>true

                    ),
                ),
                'width' => '120px'
            ))
            ->add('userProfile.lastName', Column::class, array(
                'title' => 'Soyadı',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button'=>true

                    ),
                ),
                'width' => '120px'
            ))
            ->add('userProfile.tckn', Column::class, array(
                'title' => 'T.C. No',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button'=>true

                    ),
                ),
                'width' => '100px'
            ))
            ->add('userProfile.extension', Column::class, array(
                'title' => 'Dahili',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button'=>true

                    ),
                ),
                'width' => '90px'
            ))
            ->add('teamId.name', Column::class, array(
                'title' => 'Takım',
                'visible' => true,
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->teams,
                    'search_type' => 'eq',
                    'cancel_button' => true,
                )),
                'width' => '100px'
            ))
            ->add('month', VirtualColumn::class, array(
                'title' => 'A',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                    ),
                ),
            ))
            ->add('prevMonth', VirtualColumn::class, array(
                'title' => 'O',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('period', VirtualColumn::class, array(
                'title' => 'D',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ))->add('thisYear', VirtualColumn::class, array(
                'title' => 'Y',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('lastYear', VirtualColumn::class, array(
                'title' => 'T',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('waiting', VirtualColumn::class, array(
                'title' => 'B',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('changefilter', VirtualColumn::class, array(
                'title' => 'Seç',
                'class_name'=>'changefilterButton',
                'visible' => true,
            ))
            ->add('checkEva', VirtualColumn::class, array(
                'title' => 'Detay',
                'class_name'=>'checkEvaButton',
                'visible' => true,
            ));

    }

    public function getTeamList()
    {
        /**
         * @var User $activeUser
         */
        $activeUser = $this->getUser();
        $userRoles = $activeUser->getRoles();
        if (array_search("ROLE_SEE_ALL_TEAM",$userRoles) != false){
            $teams = $this->getEntityManager()->getRepository(Team::class)->findAll();
        }else{
            $teams = $this->getEntityManager()->getRepository(Team::class)->createQueryBuilder("t")
                ->where("t.manager=:manager")
                ->setParameter("manager", $activeUser)
                ->orWhere("t.managerBackup=:managerBackup")
                ->setParameter("managerBackup", $activeUser->getTeamId()->getManagerBackup())
                ->getQuery()->getResult();
        }
        $options = [];
        /**
         * @var Team $team
         */
        foreach ($teams as $team) {
            $options[$team->getName()] = $team->getName();
        }

        $this->teams = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'evaluation_summary_datatable';
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

}