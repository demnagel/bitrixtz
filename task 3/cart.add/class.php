<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;

class CustomAddCartComponent extends CBitrixComponent implements Controllerable
{

    /** Служебный метод для проверки ajax
     * @return array
     */
    public function configureActions() : array
    {
        $prefilter = [
            new ActionFilter\Authentication(),
            new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
            new ActionFilter\Csrf(),
        ];

        return [
            'addProductsAction' => [
                'prefilters' => $prefilter,
            ]
        ];
    }


    /** AJAX Добавляет товары в корзину
     * @param array $productsId
     * @return array
     */
    public function addProductsAction(array $productsId) : array
    {
        $status = [];
        if(\Bitrix\Main\Loader::IncludeModule("sale")){

            foreach ($productsId as $id){

                $fields = [
                    'PRODUCT_ID' => $id,
                    'QUANTITY' => 1
                ];

                $res = \Bitrix\Catalog\Product\Basket::addProduct($fields);

                if (!$res->isSuccess()) {
                    $status['error'][$id] = $res->getErrorMessages();
                }
                else{
                    $status['success'][] = $id;
                }
            }
        }

        return compact('status');
    }


    public function executeComponent()
    {
        \Bitrix\Main\UI\Extension::load("ui.vue");
        if ($this->StartResultCache()) {
            $this->arResult['COMPONENT_NAME'] = $this->getName();
            $prms = [
                'select' => ['NAME', 'XML_ID', 'ID'],
                'filter' => ['=IBLOCK_ID' => $this->arParams['IBLOCK_ID']]
            ];
            $data = \Bitrix\Iblock\ElementTable::getList($prms);
            while($item = $data->fetch()){
                $this->arResult['ITEMS'][$item['XML_ID']] = [
                    'id' => $item['ID'],
                    'name' => $item['NAME'],
                ];
            }
            $this->includeComponentTemplate();
        }
    }
}