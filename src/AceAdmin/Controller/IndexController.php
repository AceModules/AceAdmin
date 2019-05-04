<?php

namespace AceAdmin\Controller;

use AceAdmin\Form\Buttons;
use AceAdmin\Form\Search;
use Ace\Datagrid\Datagrid;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Zend\Form\Element\Hidden;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Exception\InvalidArgumentException;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\JsonModel;
use Zend\View\HelperPluginManager as ViewHelperManager;

class IndexController extends AbstractActionController
{
    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * @var Datagrid
     */
    protected $datagrid;

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * @return string
     */
    public function getEntityClassName()
    {
        if (!$this->entityClassName) {
            $entity = $this->params()->fromRoute('entity');
            $config = $this->getServiceLocator()->get('config');

            if (!isset($config['admin_entities'][$entity])) {
                throw new InvalidArgumentException(sprintf('No entity configuration found for "%s"', $entity));
            }

            $this->entityClassName = $config['admin_entities'][$entity];
        }

        return $this->entityClassName;
    }

    /**
     * @return Datagrid
     */
    public function getDatagrid()
    {
        if (!$this->datagrid) {
            $datagridManager = $this->getServiceLocator()->get('DatagridManager');
            $this->datagrid = $datagridManager->create($this->getEntityClassName());
        }

        return $this->datagrid;
    }

    /**
     * @return ViewHelperManager
     */
    public function getViewHelperManager()
    {
        return $this->getServiceLocator()->get('ViewHelperManager');
    }

    /**
     * @param  MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->getViewHelperManager()->get('headLink')
            ->prependStylesheet($this->getRequest()->getBaseUrl(). '/css/ace-admin.css')
            ->prependStylesheet($this->getRequest()->getBaseUrl(). '/css/bootstrap-select-remote.min.css')
            ->prependStylesheet($this->getRequest()->getBaseUrl(). '/css/bootstrap-select.min.css')
            ->prependStylesheet($this->getRequest()->getBaseUrl(). '/css/summernote.min.css');

        $this->getViewHelperManager()->get('headScript')
            ->prependFile($this->getRequest()->getBaseUrl(). '/js/ace-admin.js')
            ->prependFile($this->getRequest()->getBaseUrl(). '/js/bootstrap-select-remote.min.js')
            ->prependFile($this->getRequest()->getBaseUrl(). '/js/bootstrap-select.min.js')
            ->prependFile($this->getRequest()->getBaseUrl(). '/js/jquery-mask.min.js')
            ->prependFile($this->getRequest()->getBaseUrl(). '/js/summernote.min.js');

        return parent::onDispatch($e);
    }

    /**
     * @return array
     */
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('config');

        if (!isset($config['admin_entities']) || !count($config['admin_entities'])) {
            throw new InvalidArgumentException('No entities have been configured');
        }

        $datagridManager = $this->getServiceLocator()->get('DatagridManager');
        $entities = [];

        foreach ($config['admin_entities'] as $entityName => $entityClassName) {
            $entities[$entityName] = $datagridManager->get($entityClassName)->getPluralName();
        }

        asort($entities);

        return [
            'result' => $entities,
        ];
    }

    /**
     * @return array
     */
    public function listAction()
    {
        $search = $this->params()->fromQuery('q');
        $page = (int)$this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort');

        $queryBuilder = $this->getDatagrid()->createSearchQueryBuilder($search, $sort);
        $paginator = new Paginator(new DoctrineAdapter(new ORMPaginator($queryBuilder)));
        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        return [
            'singular' => $this->getDatagrid()->getSingularName(),
            'plural' => $this->getDatagrid()->getPluralName(),
            'columns' => $this->getDatagrid()->getHeaderColumns(),
            'form' => new Search(),
            'result' => $paginator,
            'search' => $search,
            'page' => $page,
            'sort' => $sort,
        ];
    }

    /**
     * @return array
     */
    public function addAction()
    {
        $this->layout('layout/modal');

        $className = $this->getEntityClassName();
        $entity = new $className();

        if ($entity instanceof ServiceLocatorAwareInterface) {
            $entity->setServiceLocator($this->getServiceLocator());
        }

        $builder = new AnnotationBuilder($this->getEntityManager());
        $form = $builder->createForm($className);
        $form->setAttribute('action', $this->url()->fromRoute(null, [], true));
        $form->setHydrator(new DoctrineHydrator($this->getEntityManager(), $className));
        $form->add(new Buttons('Add'));
        $form->bind($entity);

        // TODO validate unique fields
        // TODO validate associations

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->persist($entity);
                $this->getEntityManager()->flush();
                return $this->response;
            }
        }

        return [
            'singular' => $this->getDatagrid()->getSingularName(),
            'plural' => $this->getDatagrid()->getPluralName(),
            'form' => $form,
        ];
    }

    /**
     * @return array
     */
    public function editAction()
    {
        $this->layout('layout/modal');

        $className = $this->getEntityClassName();
        $id = (int)$this->params()->fromRoute('id');
        $entity = $this->getEntityManager()->find($className, $id);

        $builder = new AnnotationBuilder($this->getEntityManager());
        $form = $builder->createForm($entity);
        $form->setAttribute('action', $this->url()->fromRoute(null, [], true));
        $form->setHydrator(new DoctrineHydrator($this->getEntityManager(), $className));
        $form->add(new Buttons('Edit'));
        $form->bind($entity);

        // TODO validate unique fields
        // TODO validate associations

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->persist($entity);
                $this->getEntityManager()->flush();
                return $this->response;
            }
        }

        return [
            'singular' => $this->getDatagrid()->getSingularName(),
            'plural' => $this->getDatagrid()->getPluralName(),
            'form' => $form,
            'entity' => $entity,
        ];
    }

    /**
     * @return array
     */
    public function deleteAction()
    {
        $this->layout('layout/modal');

        $className = $this->getEntityClassName();
        $id = (int)$this->params()->fromRoute('id');
        $entity = $this->getEntityManager()->find($className, $id);

        $form = new Form();
        $form->setAttribute('action', $this->url()->fromRoute(null, [], true));
        $form->add(new Hidden('entity'));
        $form->add(new Buttons('Delete', 'btn-danger'));

        // TODO validate no M:1 references
        // TODO validate no M:M references

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->remove($entity);
                $this->getEntityManager()->flush();
                return $this->response;
            }
        }

        $form->setData(['entity' => $id]);
        if (!$form->isValid()) {
            $form->get('buttons')->get('submit')->setAttribute('disabled', true);
        }

        return [
            'singular' => $this->getDatagrid()->getSingularName(),
            'plural' => $this->getDatagrid()->getPluralName(),
            'form' => $form,
            'entity' => $entity,
        ];
    }

    /**
     * @return array
     */
    public function suggestAction()
    {
        $search = $this->params()->fromQuery('q', '');

        $queryBuilder = $this->getDatagrid()->createSuggestQueryBuilder($search);
        $result = $queryBuilder->getQuery()->getResult();
        foreach ($result as $id => $entity) {
            $result[$id] = [
                'value' => $id,
                'text' => (string)$entity,
                'data' => [
                    'subtext' => (method_exists($entity, 'getRegNumber') ? $entity->getRegNumber() : $id),
                ],
            ];
        }

        return new JsonModel(array_values($result));
    }
}