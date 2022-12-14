<?php

namespace App\Repository;

use App\Entity\Area;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use PhpMyAdmin\SqlParser\Components\Array2d;
use PHPZlc\PHPZlc\Doctrine\ORM\Repository\AbstractServiceEntityRepository;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rule;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rules;
use PHPZlc\PHPZlc\Responses\Responses;
use PHPZlc\Validate\Validate;

/**
 * @method Area|null find($id, $lockMode = null, $lockVersion = null)
 * @method Area|null findOneBy(array $criteria, array $orderBy = null)
 * @method Area|null    findAssoc($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Area|null   findLastAssoc($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Area|null    findAssocById($id, $rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Area[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Area[]    findAll($rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 * @method Area[]    findLimitAll($rows, $page = 1, $rules = null, ResultSetMappingBuilder $resultSetMappingBuilder = null, $aliasChain = '')
 */
class AreaRepository extends AbstractServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Area::class);
        $this->telSqlArray['finalOrderBy'] = 'sql_pre.level ASC, sql_pre.first_letter ASC, sql_pre.name ASC, ' . $this->telSqlArray['finalOrderBy'];
    }


    public function registerRules()
    {
        $this->registerNecessaryRule(new Rule('necessary_where', 1));
        $this->registerCoverRule('necessary_where');
    }

    public function ruleRewrite(Rule $currentRule, Rules $rules, ResultSetMappingBuilder $resultSetMappingBuilder)
    {
        if($this->ruleMatch($currentRule, 'necessary_where')){
            $this->sqlArray['where'] .= " AND sql_pre.id <> 0";
        }
    }

    /**
     * ????????????id?????????????????????
     *
     * @param int $parent_id
     * @return Area[]
     */
    public function findArrayToParentId($parent_id = 0)
    {
        if(empty($parent_id)){
            $parent_id = 0;
        }

        return $this->arraySerialization($this->findAll(
            ['parent_id' => $parent_id]
        ));
    }

    /**
     * ????????????
     */
    public function builtInData()
    {
        $data = file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Business' . DIRECTORY_SEPARATOR . 'AreaBusiness' . DIRECTORY_SEPARATOR . 'area.json');
        $areas = json_decode($data, true);

        $conn = $this->getEntityManager()->getConnection();

        foreach ($areas as $area){
            $conn->insert($this->getTableName(), $area);
        }

        $conn->executeUpdate("UPDATE area set parent_id = null WHERE id = 0");

        $conn->executeUpdate(
            "UPDATE area set level = 1 WHERE parent_id = 0 AND id <> 1"
        );

        $l1Ps = $conn->fetchAllAssociative("select id from area where level = 1");
        foreach ($l1Ps as $p){
            $conn->executeUpdate("UPDATE area set level = 2 WHERE parent_id = ?", array($p['id']));
        }

        $l2PIds = $conn->fetchAllAssociative("select id from area where level = 2");
        foreach ($l2PIds as $p){
            $conn->executeUpdate("UPDATE area set level = 3 WHERE parent_id = ?", array($p['id']));
        }
    }

    /**
     * ??????????????????area
     *
     * @param $name
     * @param int $level ?????????????????? null ?????? 1 ??? 2 ??? 3 ???
     * @param array $rules
     * @return Area|null
     */
    public function findToName($name, $level = null, $rules = [])
    {
        switch ($level){
            case 1:
                $name = rtrim($name, '???');
                break;
            case 2:
                $name = rtrim($name, '???');
                break;
            case 3:
                $name = rtrim($name, '???');
                $name = rtrim($name, '???');
                break;
        }

        $rules = array_merge($rules, [
            'name' . Rule::RA_LIKE => "%{$name}%",
            'level' . Rule::RA_NOT_REAL_EMPTY  => $level,
        ]);

        return $this->findAssoc($rules);
    }

    /**
     * @param Area[] $areas
     * @return array
     */
    public function getArrayChildren($areas)
    {
        $data = [];

        foreach ($areas as $area) $data[$area->getId()] = [
            'id' => $area->getId(),
            'name' => $area->getName(),
            'parent_id' => $area->getParent()->getId(),
        ];

        foreach ($data as $id => $area) {
            $data[$area['parent_id']]['child'][$area['id']] = & $data[$id];
        }

        foreach ($data as $key => $value) {
            if (array_key_exists('parent_id', $value)) {
                unset($data[$key]);
            }
        }

        return $this->fixKeys($data);
    }

    /**
     * ??????????????????????????????????????????
     *
     * @param Area[] $areas
     * @return array
     */
    public function getArrayFirstLetterGroup($areas)
    {
        $cities = [];
        foreach ($areas as $area){
            if(!empty($area->getFirstLetter())){
                $cities[$area->getFirstLetter()][] = $this->toArray($area);
            }
        }
        ksort($cities);

        return $cities;
    }

    /**
     * ??????key
     *
     * ?????????????????????????????????key????????????????????????????????????????????????value????????????????????????
     * @param $array
     * @return array
     */
    private function fixKeys($array)
    {
        $numberCheck = false;
        foreach ($array as $k => $val) {
            if (is_array($val)) {
                $array[$k] = $this->fixKeys($val); //??????
                if (is_numeric($k)) $numberCheck = TRUE;
            }
        }

        if ($numberCheck === true) {
            return array_values($array);
        } else {
            return $array;
        }
    }

    /**
     * @param Area $entity
     * @return array
     */
    public function toArrayApi($entity): array
    {
        return array(
            'id' => $entity->getId(),
            'name' => $entity->getName()
        );
    }


}
