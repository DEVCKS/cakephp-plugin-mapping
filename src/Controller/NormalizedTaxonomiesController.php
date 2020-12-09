<?php 

namespace Mapping\Controller;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class NormalizedTaxonomiesController extends AppController
{
    public function indexCpv()
    {
        $ids = $this->request->getQuery('ids');

        if (is_null($ids)) {
            $ids = [];
        }

        $conn = ConnectionManager::get('normalized_mapping');
        $repo = TableRegistry::getTableLocator()->get('Mapping.Cpvs', ['connection' => $conn]);
        $result = $repo->getByIds($ids);
        
        $this->setResponseInJson($result);

        return $this->response;
    }

    public function indexNaf()
    {
        $ids = $this->request->getQuery('ids');

        if (is_null($ids)) {
            $ids = [];
        }

        $conn = ConnectionManager::get('normalized_mapping');
        $repo = TableRegistry::getTableLocator()->get('Mapping.Nafs', ['connection' => $conn]);
        $result = $repo->getByIds($ids);
        
        $this->setResponseInJson($result);

        return $this->response;
    }

    public function indexNacre()
    {
        $ids = $this->request->getQuery('ids');

        if (is_null($ids)) {
            $ids = [];
        }

        $conn = ConnectionManager::get('normalized_mapping');
        $repo = TableRegistry::getTableLocator()->get('Mapping.Nacres', ['connection' => $conn]);
        $result = $repo->getByIds($ids);
        
        $this->setResponseInJson($result);

        return $this->response;
    }
}