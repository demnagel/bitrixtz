<?php

namespace Shop\Main\Helper\Hl;

use Shop\Main\Helper\Hl\Abstractions\HlAbstract;

class ComparisonProductsForUser extends HlAbstract
{
    /**
     * @var string
     */
    protected $nameEntity = 'ComparsionProducts';

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $tag;

    /**
     * FavoriteProductsForUser constructor.
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        parent::__construct();
        $this->userId = $userId;
        $this->tag = __CLASS__ . $userId;
    }

    /**
     * @return string
     */
    public function getTag() : string
    {
        return $this->tag;
    }

    /** Получение списка id избранных товаров для пользователя
     * @return array
     */
    public function getIdProducts() : array
    {
        $result = [];
        if($this->userId){
            $params = [
                'select' => ['UF_PRODUCT_ID'],
                'filter' => ['=UF_USER_ID' => $this->userId]
            ];
            $data = $this->getElements($params)->fetchAll();
            $result = array_column($data, 'UF_PRODUCT_ID');
        }
        return $result;
    }

    /** Проверяет товар на существование и активность
     * @param int $product_id
     * @return bool
     */
    protected function checkReal(int $product_id) : bool
    {
        $params = [
            'filter' => ['=ID' => $product_id, '=ACTIVE' => true],
            'count_total' => true,
        ];
        return boolval(\Bitrix\Iblock\ElementTable::getList($params)->getCount());
    }

    /** Определяет наличие избранного товара у пользователя
     * @param $product_id
     * @return int id существующей записи или 0
     */
    public function checkProduct(int $product_id): int
    {
        $params = [
            'select' => ['ID'],
            'filter' => [
                '=UF_USER_ID' => $this->userId,
                '=UF_PRODUCT_ID' => $product_id
            ],
        ];
        $result = $this->getElements($params)->fetch();
        return intval($result['ID']);
    }

    /** Гарантирует присутствие товара в избранных у пользователя
     * @param int $product_id
     * @return bool
     */
    public function addProduct(int $product_id) : bool
    {
        if($this->userId){
            if($this->checkProduct($product_id)){
                return true;
            }
            else{
                if($this->checkReal($product_id)){
                    $params = [
                        'UF_USER_ID' => $this->userId,
                        'UF_PRODUCT_ID' => $product_id
                    ];
                    return $this->add($params)->isSuccess();
                }
            }
        }
        return false;
    }

    /** Гарантирует отсутствие товара в избранных у пользователя
     * @param int $product_id
     * @return bool
     */
    public function delProduct(int $product_id) : bool
    {
        if($this->userId){
            if($id = $this->checkProduct($product_id)){
                return $this->delete($id)->isSuccess();
            }
            else{
                return true;
            }
        }
        return false;
    }
}