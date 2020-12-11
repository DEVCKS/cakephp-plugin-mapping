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
                    $taxoIdLabel = strtolower($taxo)."_id";
                    $entityIdLabel = substr(strtolower($entity), 0, -1)."_id";

                    try {
                        $conn->execute('SELECT 1 FROM `'.$tableLabel.'`');
                    } catch(\Exception $e) {
                        try {
                            
                            $sql = "CREATE TABLE `".$tableLabel."`";
                            $sql .= "(`".$taxoIdLabel."` INT(11) UNSIGNED NOT NULL";
                            $sql .= ", `".$entityIdLabel."` INT(11) NOT NULL) ENGINE = InnoDB;";
    
                            // create table
                            $conn->execute($sql);
    
                            // add unique index
                            $conn->execute("ALTER TABLE `".$tableLabel."` ADD UNIQUE( `".$taxoIdLabel."`, `".$entityIdLabel."`);");  
                            
                            // add foreign key to model
                            $conn->execute("ALTER TABLE `".$tableLabel."` ADD CONSTRAINT `".$tableLabel."_ibfk_1` FOREIGN KEY (`".$entityIdLabel."`) REFERENCES `".strtolower($entity)."`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;");
                        } catch (\Exception $e) {
                            debug($e->getMessage());
                            return false;
                        }
                    }

                    $entityLabel = '';
                    $entityTableLabel = '';
                    foreach (explode('_', $tableLabel) as $underscored) {
                        $entityLabel .= ucfirst($underscored);
                    }

                    $entityTableLabel = $entityLabel;
                    $entityTableLabel.= 'Table';
                    $entityLabel = substr($entityLabel, 0, -1);
                    
                    $entityPath = dirname(dirname(__FILE__))."/Model/Entity/".$entityLabel.".php";
                    if (!file_exists($entityPath)) {
                        file_put_contents($entityPath, $this->getEntityFileContent($entityLabel, $taxoIdLabel, $entityIdLabel));
                    }

                    $tablePath = dirname(dirname(__FILE__))."/Model/Table/".$entityTableLabel.".php";
                    if (!file_exists($tablePath)) {
                        file_put_contents($tablePath, $this->getTableFileContent($entityTableLabel, $tableLabel, $taxoIdLabel, $entityIdLabel));
                    }
                }
            }
        });
    }

    /**
     * @param string $entityLabel
     * @param string $taxoIdLabel
     * @param string $entityIdLabel
     * @return string
     */
    public function getEntityFileContent(string $entityLabel, string $taxoIdLabel, string $entityIdLabel): string
    {
        $taxoIdGetter = '';
        $entityIdGetter = '';

        foreach (explode('_', $taxoIdLabel) as $underscored) {
            $taxoIdGetter .= ucfirst($underscored);
        }

        foreach (explode('_', $entityIdLabel) as $underscored) {
            $entityIdGetter .= ucfirst($underscored);
        }


        return "<?php

        namespace Mapping\Model\Entity;
        
        use Cake\ORM\Entity;
        
        /**
         * @property int ".$taxoIdLabel."
         * @property int ".$entityIdLabel."
         */
        class ".$entityLabel." extends Entity
        {
        
            /**
             * Fields that can be mass assigned using newEntity() or patchEntity().
             *
             * Note that when '*' is set to true, this allows all unspecified fields to
             * be mass assigned. For security purposes, it is advised to set '*' to false
             * (or remove it), and explicitly make individual fields accessible as needed.
             *
             * @var array
             */
            protected \$_accessible = [
                '".$taxoIdLabel."' => true,
                '".$entityIdLabel."' => true,
            ];
        
            /**
             * @return int
             */
            public function get".$taxoIdGetter."(): int
            {
                return \$this->".$taxoIdLabel.";
            }
        
            /**
             * @return int
             */
            public function get".$entityIdGetter."(): int
            {
                return \$this->".$entityIdLabel.";
            }
        }";
    }

    /**
     * @param string $entityTableLabel
     * @param string $tableLabel
     * @param string $taxoIdLabel
     * @param string $entityIdLabel
     * @return string
     */
    public function getTableFileContent(string $entityTableLabel, string $tableLabel, string $taxoIdLabel, string $entityIdLabel): string
    {
        return "<?php

        namespace Mapping\Model\Table;
        
        use Cake\ORM\Table;
        
        class ".$entityTableLabel." extends Table
        {
            use tMappingTable;

            /**
             * Initialize method
             *
             * @param array \$config The configuration for the Table.
             * @return void
             */
            public function initialize(array \$config)
            {
                parent::initialize(\$config);
        
                \$this->setTable('".$tableLabel."');
                \$this->setPrimaryKey(['".$taxoIdLabel."', '".$entityIdLabel."']);

                \$this->taxonomyIdLabel = '".$taxoIdLabel."';
                \$this->entityIdLabel = '".$entityIdLabel."';
            }
        }";
    }
}
