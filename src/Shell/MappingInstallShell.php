<?php

namespace Mapping\Shell;

use Cake\Console\Shell;
use Mapping\MappingConfig;
use Cake\Datasource\ConnectionManager;

class MappingInstallShell extends Shell
{
    /**
     * @return void
     */
    public function install(): void
    {      
        $this->info('Successfully installed !');
        
        $conf = MappingConfig::getConfig();
        $conn = ConnectionManager::get('default');
        $conn->transactional(function() use (&$conn, $conf) {
            // check that all mapping tables are created, create them if not
            foreach ($conf['entities_mapped'] as $taxo => $entities) {
                foreach ($entities as $entity) {
                    $tableLabel = "mapping_".strtolower($taxo)."_".strtolower($entity);
                    try {
                        $conn->execute('SELECT 1 FROM `'.$tableLabel.'`');
                    } catch(\Exception $e) {
                        try {
                            $taxoIdLabel = strtolower($taxo)."_id";
                            $entitIdLabel = substr(strtolower($entity), 0, -1)."_id";
                            $sql = "CREATE TABLE `".$tableLabel."`";
                            $sql .= "(`".$taxoIdLabel."` INT(11) UNSIGNED NOT NULL";
                            $sql .= ", `".$entitIdLabel."` INT(11) NOT NULL) ENGINE = InnoDB;";
    
                            // create table
                            $conn->execute($sql);
    
                            // add unique index
                            $conn->execute("ALTER TABLE `".$tableLabel."` ADD UNIQUE( `".$taxoIdLabel."`, `".$entitIdLabel."`);");  
                            
                            // add foreign key to model
                            $conn->execute("ALTER TABLE `".$tableLabel."` ADD CONSTRAINT `".$tableLabel."_ibfk_1` FOREIGN KEY (`".$entitIdLabel."`) REFERENCES `".strtolower($entity)."`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;");
                        } catch (\Exception $e) {
                            debug($e->getMessage());
                            return false;
                        }
                    }
                }
            }
        });
    }
}
