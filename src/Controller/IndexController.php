<?php

namespace AceAdmin\Controller;

use AceAdmin\Form\Buttons;
use AceAdmin\Form\Search;
use AceDatagrid\DatagridManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Mapping\Annotation\Loggable;
use Zend\Form\Element\Hidden;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\View\Exception\InvalidArgumentException;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DatagridManager
     */
    private $datagridManager;

    /**
     * @var AnnotationBuilder
     */
    private $annotationBuilder;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $entityClassName;

    /**
     * @var string
     */
    private $logEntryClassName;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, AnnotationBuilder $annotationBuilder, array $options = [])
    {
        $this->entityManager     = $entityManager;
        $this->datagridManager   = new DatagridManager($entityManager);
        $this->annotationBuilder = $annotationBuilder;
        $this->options           = $options;
    }

    /**
     * @return string
     */
    public function getEntityClassName()
    {
        if (!$this->entityClassName) {
            $entity = $this->params()->fromRoute('entity');

            if (!isset($this->options['entities'][$entity])) {
                throw new InvalidArgumentException(sprintf('No entity configuration found for "%s"', $entity));
            }

            $this->entityClassName = $this->options['entities'][$entity];
        }

        return $this->entityClassName;
    }

    /**
     * @return string
     */
    public function getLogEntryClassName($softCheck = false)
    {
        if (!$this->logEntryClassName) {
            $className = $this->getEntityClassName();

            $reader = new AnnotationReader();
            $reflection = new \ReflectionClass($className);
            $loggable = $reader->getClassAnnotation($reflection, Loggable::class);

            if (!$loggable) {
                if ($softCheck) {
                    return;
                }
                throw new InvalidArgumentException(sprintf('No log entry class has been configured for %s', $className));
            }

            $this->logEntryClassName = $loggable->logEntryClass ?? LogEntry::class;
            if ($loggable->logEntryClass && !class_exists($loggable->logEntryClass)) {
                $this->logEntryClassName = $reflection->getNamespaceName() . '\\' . $loggable->logEntryClass;
            }
        }

        return $this->logEntryClassName;
    }

    /**
     * @return array
     */
    public function indexAction()
    {
        if (!isset($this->options['entities']) || !count($this->options['entities'])) {
            throw new InvalidArgumentException('No entities have been configured');
        }

        $entities = [];

        foreach ($this->options['entities'] as $entitySlug => $entityClassName) {
            $entities[$entitySlug] = $this->datagridManager->get($entityClassName)->getPluralName();
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
        $className = $this->getEntityClassName();
        $datagrid = $this->datagridManager->get($className);

        $search = $this->params()->fromQuery('q');
        $page = (int) $this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort');

        $queryBuilder = $datagrid->createSearchQueryBuilder($search, $sort);
        $paginator = new Paginator(new DoctrineAdapter(new ORMPaginator($queryBuilder)));
        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        $hasLog = (bool) $this->getLogEntryClassName(true);

        return [
            'singular' => $datagrid->getSingularName(),
            'plural'   => $datagrid->getPluralName(),
            'columns'  => $datagrid->getHeaderColumns(),
            'form'     => new Search(),
            'result'   => $paginator,
            'search'   => $search,
            'page'     => $page,
            'sort'     => $sort,
            'hasLog'   => $hasLog,
        ];
    }

    /**
     * @return array
     */
    public function addAction()
    {
        $this->layout('layout/modal');

        $className = $this->getEntityClassName();
        $datagrid = $this->datagridManager->get($className);
        $entity = new $className();

        $form = $this->annotationBuilder->createForm($className);
        $form->setAttribute('action', $this->url()->fromRoute(null, [], true));
        $form->setHydrator(new DoctrineHydrator($this->entityManager, $className));
        $form->add(new Buttons('Add'));
        $form->bind($entity);

        // TODO validate unique fields
        // TODO validate associations

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                return $this->response;
            }
        }

        return [
            'singular' => $datagrid->getSingularName(),
            'plural'   => $datagrid->getPluralName(),
            'form'     => $form,
        ];
    }

    /**
     * @return array
     */
    public function editAction()
    {
        $this->layout('layout/modal');

        $className = $this->getEntityClassName();
        $datagrid = $this->datagridManager->get($className);
        $id = (int) $this->params()->fromRoute('id');
        $entity = $this->entityManager->find($className, $id);

        $form = $this->annotationBuilder->createForm($entity);
        $form->setAttribute('action', $this->url()->fromRoute(null, [], true));
        $form->setHydrator(new DoctrineHydrator($this->entityManager, $className));
        $form->add(new Buttons('Edit'));
        $form->bind($entity);

        // TODO validate unique fields
        // TODO validate associations

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                return $this->response;
            }
        }

        return [
            'singular' => $datagrid->getSingularName(),
            'plural'   => $datagrid->getPluralName(),
            'form'     => $form,
            'entity'   => $entity,
        ];
    }

    /**
     * @return array
     */
    public function deleteAction()
    {
        $this->layout('layout/modal');

        $className = $this->getEntityClassName();
        $datagrid = $this->datagridManager->get($className);
        $id = (int) $this->params()->fromRoute('id');
        $entity = $this->entityManager->find($className, $id);

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
                $this->entityManager->remove($entity);
                $this->entityManager->flush();
                return $this->response;
            }
        }

        $form->setData(['entity' => $id]);
        if (!$form->isValid()) {
            $form->get('buttons')->get('submit')->setAttribute('disabled', true);
        }

        return [
            'singular' => $datagrid->getSingularName(),
            'plural'   => $datagrid->getPluralName(),
            'form'     => $form,
            'entity'   => $entity,
        ];
    }

    /**
     * @return array
     */
    public function historyAction()
    {
        $className = $this->getEntityClassName();
        $logEntryClassName = $this->getLogEntryClassName();
        $datagrid = $this->datagridManager->get($className);
        $id = (int) $this->params()->fromRoute('id');
        $entity = $this->entityManager->find($className, $id);
        $result = $this->entityManager->getRepository($logEntryClassName)->getLogEntries($entity);

        return [
            'singular' => $datagrid->getSingularName(),
            'plural'   => $datagrid->getPluralName(),
            'entity'   => $entity,
            'result'   => $result,
        ];
    }

    /**
     * @return array
     */
    public function revertAction()
    {
        $this->layout('layout/modal');

        $className = $this->getEntityClassName();
        $logEntryClassName = $this->getLogEntryClassName();
        $datagrid = $this->datagridManager->get($className);
        $id = (int) $this->params()->fromRoute('id');
        $version = (int) $this->params()->fromRoute('version');
        $entity = $this->entityManager->find($className, $id);

        $form = new Form();
        $form->setAttribute('action', $this->url()->fromRoute(null, [], true));
        $form->add(new Hidden('entity'));
        $form->add(new Buttons('Rollback', 'btn-primary'));

        // TODO validate version exists

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->entityManager->getRepository($logEntryClassName)->revert($entity, $version);
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                return $this->response;
            }
        }

        $form->setData(['entity' => $id]);
        if (!$form->isValid()) {
            $form->get('buttons')->get('submit')->setAttribute('disabled', true);
        }

        return [
            'singular' => $datagrid->getSingularName(),
            'plural'   => $datagrid->getPluralName(),
            'form'     => $form,
            'entity'   => $entity,
            'version'  => $version,
        ];
    }

    /**
     * @return array
     */
    public function suggestAction()
    {
        $className = $this->getEntityClassName();
        $datagrid = $this->datagridManager->get($className);

        $search = $this->params()->fromQuery('q', '');
        $criteria = $this->params()->fromQuery();
        unset($criteria['q']);

        $queryBuilder = $datagrid->createSuggestQueryBuilder($search, 5, false, $criteria);
        $result = $queryBuilder->getQuery()->getResult();
        foreach ($result as $id => $entity) {
            $result[$id] = [
                'value' => $id,
                'text'  => (string) $entity,
                'data'  => [
                    'subtext' => (method_exists($entity, 'getSuggestSubtext') ? $entity->getSuggestSubtext() : 'id: ' . $id),
                ],
            ];
        }

        return new JsonModel(array_values($result));
    }
}
