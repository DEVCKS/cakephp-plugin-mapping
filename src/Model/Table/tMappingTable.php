<?php

namespace Mapping\Model\Table;

trait tMappingTable 
{
    /** @var string */
    protected $taxonomyIdLabel;

    /** @var string */
    protected $entityIdLabel;

    /**
     * @param array $entityIds = []
     * @param Callable|null $scopeFunction must accept entityIds at paremeter and return boolean true if all in scope
     * @return array 
     */
    public function getByEntityIds(array $entityIds = [], ?Callable $scopeFunction = null): array
    {
        if (count($entityIds) == 0) {
            return [];
        }

        $scope = true;
        if (!is_null($scopeFunction)) {
            $scope = $scopeFunction($entityIds);
        }
           
        if ($scope) {
            return $this->find()
            ->where([
                $this->entityIdLabel.' IN' => $entityIds
            ])->toArray();
        }

        return [];
    }

    /**
     * @param array $taxonomyIds = []
     * @param Callable|null $scopeFunction must accept entityIds at paremeter and return boolean true if all in scope
     * @return array 
     */
    public function getByTaxonomyIds(array $taxonomyIds = [], ?Callable $scopeFunction = null): array
    {
        if (count($taxonomyIds) == 0) {
            return [];
        }
           
        $entities = $this->find()
            ->where([
                $this->taxonomyIdLabel.' IN' => $taxonomyIds
            ])->toArray();

        $scope = true;
        if (!is_null($scopeFunction)) {
            $entityIdLabel = $this->entityIdLabel;
            $entityIds = [];

            foreach ($entities as $entity) {
                $entityIds[] = $entity->$entityIdLabel;
            }

            $scope = $scopeFunction($entityIds);
        }
        
        if ($scope) {
            return $entities;
        }
        
        return [];
    }

    /**
     * @param array $datas
     * @param Callable|null $scopeFunction must accept entityIds at paremeter and return boolean true if all in scope
     * @return array
     */
    public function add(array $datas, ?Callable $scopeFunction = null): array
    {
        $scope = true;
        if (!is_null($scopeFunction)) {
            $entityIds = [];
            foreach ($datas as $data) {
                $entityIds[] = $data[$this->entityIdLabel];
            }

            $scope = $scopeFunction($entityIds);
        }
        
        if ($scope) {
            $entities = $this->newEntities($datas);

            foreach ($entities as $e) {
                if ($e->getErrors()) {
                    throw new \Exception(json_encode($e->getErrors()), 403);
                }
            }
            
            $entities = $this->saveMany($entities);
    
            foreach ($entities as $e) {
                if ($e->hasErrors()) {                
                    throw new \Exception(json_encode($e->getErrors()), 500);
                }
            }
            
            return $entities;
        }

        return [];
    }

    /**
     * @param array $entityIds
     * @param Callable|null $scopeFunction must accept entityIds at paremeter and return boolean true if all in scope
     * @return void
     */
    public function deleteByEntityIds(array $entityIds, ?Callable $scopeFunction = null): void
    {
        $scope = true;

        if (!is_null($scopeFunction)) {
            $scope = $scopeFunction($entityIds);
        }

        if ($scope) {
            $this->deleteAll([
                $this->entityIdLabel.' IN' => $entityIds
            ]);
        }
    }

    /**
     * @param array $taxonomyIds
     * @param Callable|null $scopeFunction must accept entityIds at paremeter and return boolean true if all in scope
     * @return void
     */
    public function deleteByTaxonomyIds(array $taxonomyIds, ?Callable $scopeFunction = null): void
    {
        $scope = true;

        if (!is_null($scopeFunction)) {
            $entities = $this->getByTaxonomyIds($taxonomyIds);
            $entityIdLabel = $this->entityIdLabel;
            $entityIds = [];

            foreach ($entities as $entity) {
                $entityIds[] = $entity->$entityIdLabel;
            }

            $scope = $scopeFunction($entityIds);
        }

        if ($scope) {
            $this->deleteAll([
                $this->taxonomyIdLabel.' IN' => $taxonomyIds
            ]);
        }
    }
}