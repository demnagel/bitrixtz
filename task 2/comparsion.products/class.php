<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Application;
use Shop\Main\Helper\Hl\ComparisonProductsForUser;

class ComparisonProductsComponent extends CBitrixComponent implements Controllerable
{

    /** Проверка необходимых зависимостей
     * @throws Exception
     */
    private function _checkLibs() : void
    {
        if(!\Bitrix\Main\Loader::IncludeModule("shop.main")){
            throw new \Exception('Не удалось подключить модуль shop.main');
        }

        if (!class_exists('\Shop\Main\Helper\Hl\ComparisonProductsForUser')) {
            throw new \Exception('Не найден класс ComparisonProductsForUser - необходимый для работы компонента');
        }
    }

    /** Проброс доп параметров
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams) : array
    {
        global $USER;
        $arParams['USER_ID'] = $USER->GetId() ?? 0;
        $arParams['CACHE_TAG'] = __CLASS__ . $arParams['USER_ID'];
        return $arParams;
    }


    /** Создание экземпляра для работы с hl
     * @return ComparisonProductsForUser
     */
    public function favoriteProductInstance() : ComparisonProductsForUser
    {
        $this->_checkLibs();
        return new ComparisonProductsForUser($this->arParams['USER_ID']);
    }


    public function executeComponent()
    {
        if ($this->StartResultCache()) {
            $instance = $this->favoriteProductInstance();
            Application::getInstance()->getTaggedCache()->registerTag($instance->getTag());
            $this->arResult['PRODUCTS_ID'] = $instance->getIdProducts();

            //Для списка
            $prms = [
                'select' => ['*'],
                'filter' => [
                    '=IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    '=ID' => $this->arResult['PRODUCTS_ID']
                ]
            ];
            $this->arResult['ITEMS'] = \Bitrix\Iblock\ElementTable::getList($prms)->fetchAll();

        }
        $this->includeComponentTemplate();
    }


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
            'editAction' => [
                'prefilters' => $prefilter,
            ]
        ];
    }

    /** Шаблон для ответов на изменение в списке сравниваемых товаров
     * @param string $method
     * @param int $productId
     * @return array
     */
    protected function editAction(string $method, int $productId) : array
    {
        $count = 0;
        try{
            $instance = $this->favoriteProductInstance();
            if($method == 'delProduct' || $method == 'addProduct'){

                if($instance->$method($productId)){
                    Application::getInstance()->getTaggedCache()->clearByTag($instance->getTag());
                }

                $count = count($instance->getIdProducts());
            }
        }catch (Exception $e){}

        return compact('count');
    }
    
}
