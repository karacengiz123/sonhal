<?php

namespace App\Datatables;

use App\Asterisk\Entity\Cdr;
use App\Asterisk\Entity\QueueLog;
use App\Asterisk\Entity\Queues;
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
class OutboundCallDatatable extends AbstractDatatable
{
    private $agents;
    private $queues;

    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {

        $formatter = function ($row) {

            $row['srcOrg'] = isset($this->agents[$row['srcOrg']])?$this->agents[$row['srcOrg']]:'Bilinmeyen Kullanıcı';


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
            ->add('cdrId', Column::class, array(
                'title' => 'Id',
                'visible' => false,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('calldate', DateTimeColumn::class, array(
                'title' => 'Çağrı Zamanı',
                'filter' => array(DateRangeFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
            ))
            ->add('srcOrg', Column::class, array(
                'title' => 'Temsilci',
                'filter' => array(Select2Filter::class, array(
                    'select_options' => array('' => 'Hepsi') + $this->agents,
                    'search_type' => 'eq',
                    'cancel_button' => true,
                )),
            ))
            ->add('dst', Column::class, array(
                'title' => 'Arayan',

            ))
            ->add('src', Column::class, array(
                'title' => 'Aranan',

            ))
            ->add('duration', Column::class, array(
                'title' => 'Süre',

            ))


            ->add('uniqueid', Column::class, array(
                'title' => 'callid',
                'filter' => array(TextFilter::class,
                    array(
                        'cancel_button' => true,
//                        'search_type' => 'eq',
                    ),
                ),
            ))->add(null, ActionColumn::class, array(
                'title' => 'İşlemler',
                'start_html' => '<div class="start_actions">',
                'end_html' => '</div>',
                'actions' => array(
                    array(
                        'route' => 'quality_new',
                        'route_parameters' => array(
                            'callid' => 'uniqueid',
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
            ))
        ;
    }

    public function getAgentList()
    {
        $agents = $this->getEntityManager()->getRepository(UserProfile::class)->findAll();

        foreach ($agents as $agent) {
            $options[$agent->getExtension()] = $agent->getFirstName() . " " . $agent->getLastName();
        }

        $this->agents = $options;

    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return Cdr::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'outbound_call_datatable';
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