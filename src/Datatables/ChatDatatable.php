<?php

namespace App\Datatables;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
use App\Entity\Chat;
use App\Entity\User;
use App\Entity\UserProfile;
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
class ChatDatatable extends AbstractDatatable
{
    private $agents;
    private $statusses = ['Beklemede','Aktif','Tamamlanmış','Zaman Aşımı'];

    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {

        $formatter = function ($row) {

            $row['user']['id'] = isset($this->agents[$row['user']['id']])?$this->agents[$row['user']['id']]:'Bilinmeyen Kullanıcı';
            $row['actions'] = "<button onclick='chatHistoryModalStart(".$row['id'].")'>Görüntüle</button>";
            $row['status'] = $this->statusses[$row['status']];

            return $row;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $this->getAgentList();

        $this->language->set(array(
            'cdn_language_by_locale' => true,
        ));

        $this->ajax->set(array());

        $this->options->set(array(
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order_cells_top' => true,
            'order' => array(array(0, 'desc')),
            'dom' => 'Bfrtip',
        ));

        $this->features->set(array());

        $this->extensions->set(array(
            'responsive' => true,
                'buttons' => true,
            'buttons' => array(
                'show_buttons' => array('copy', 'print'),
                'create_buttons' => array(

                    array(
                        'extend' => 'csv',
                        'text' => 'custom csv button',
                    ),
                    array(
                        'extend' => 'pdf',
                        'button_options' => array(
                            'exportOptions' => array(

                            ),
                        ),
                    ),
                ),
            ),
        ));


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

            ->add('user.id', Column::class, array(
                'title' => 'Temsilci',
                'visible' => true,
                'filter' => array(Select2Filter::class,
                    array(
                        'search_type' => 'eq',
                        'select_options' => ['All'] + $this->agents,
                    ),
                ),
            ))
            ->add('tcid', Column::class, array(
                'title' => 'T.C. K.N',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                    ),
                ),
            ))

            ->add('activityId', Column::class, array(
                'title' => 'Aktivite ID',
                'visible' => true,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                    ),
                ),
            ))
            ->add('status', Column::class, array(
                'title' => 'Durum',
                'visible' => true,
                'filter' => array(SelectFilter::class,
                    array(
                        'search_type' => 'eq',
                        'select_options' => [''=>'Hepsi']+$this->statusses,
                    ),
                ),
            ))
            ->add('createdAt', DateTimeColumn::class, array(
                'title' => 'Chat Zamanı',
                'filter' => array(DateRangeFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('updatedAt', DateTimeColumn::class, array(
                'title' => 'Son Güncelleme',
                'filter' => array(DateRangeFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('actions', VirtualColumn::class ,  [
                    'title' => "Görüntüle",

                ]
            )
        ;
    }

    public function getAgentList()
    {
        $agents = $this->getEntityManager()->getRepository(UserProfile::class)->findAll();

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
        return Chat::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'chat_list';
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


}