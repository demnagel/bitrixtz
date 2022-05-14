<?php

namespace Sprint\Migration;

use Bitrix\Highloadblock\HighloadBlockTable;

class New_hl_comparsion_products_20220419140256 extends Version
{
    protected $description = "hl сравниваемых товаров";

    protected $moduleVersion = "3.22.2";

    protected $tableName = 'shop_comparsion_products';

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {

        if($this->getHlId()){
            $this->outError('Хайлоад ' . $this->tableName . ' уже существует');
            return false;
        }

        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock(array(
            'NAME' => 'ComparsionProducts',
            'TABLE_NAME' => $this->tableName,
            'LANG' =>
                array(
                    'ru' =>
                        array(
                            'NAME' => 'Сравниваемые товары',
                        ),
                ),
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_USER_ID',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'SIZE' => 20,
                    'MIN_VALUE' => 0,
                    'MAX_VALUE' => 0,
                    'DEFAULT_VALUE' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'id пользователя',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'id пользователя',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'id пользователя',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'id пользователя',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => '',
                ),
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_PRODUCT_ID',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'SIZE' => 20,
                    'MIN_VALUE' => 0,
                    'MAX_VALUE' => 0,
                    'DEFAULT_VALUE' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'id товара',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'id товара',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'id товара',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'id товара',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => '',
                ),
        ));
        $this->outSuccess(__CLASS__ . ' установка завершена');
    }

    public function down()
    {
        $this->clear();
        $this->outSuccess(__CLASS__ . ' откат завершен');
    }

    private function clear()
    {
        if($id = $this->getHlId()){
            HighloadBlockTable::delete($id);
        }

        $connection = \Bitrix\Main\Application::getConnection();
        if($connection->isTableExists($this->tableName)){
            $connection->dropTable($this->tableName);
        }
    }

    private function getHlId()
    {
        $filter = [
            'select' => ['ID'],
            'filter' => ['=TABLE_NAME' => $this->tableName]
        ];
        $hlblock = HighloadBlockTable::getList($filter)->fetch();

        if($hlblock){
            return $hlblock['ID'];
        }

        return false;
    }
}
