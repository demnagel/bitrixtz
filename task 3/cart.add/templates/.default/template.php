<?php
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var string $componentPath
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) die() ;
use \Bitrix\Main\Localization\Loc;
global $USER;

$componentData = [
    'showBtn' => false,
    'runAjax' => false,
    'products' => $arResult['ITEMS'] ?? [],
    'componentName' => $arResult['COMPONENT_NAME'],
    'fields' => [''],
    'componentMethod' => 'addProducts'
];

if($USER->IsAuthorized()): ?>
    <div id="items_wrapper" class="wrapper">

        <h2><?= Loc::getMessage("TITLE"); ?></h2>

        <div class="panel">
            <div class="btn" @click = 'add'><?= Loc::getMessage("ADD"); ?></div>
            <div v-if="showBtn" class="btn" @click = 'send'><?= Loc::getMessage("SEND"); ?></div>
        </div>

        <div class="items" v-for="(val, key) in fields">
            <div class="item">
                <span v-if="fields.length > 1" class="del" @click="del(key)">-</span>
                <input class="input" type="text"
                       v-bind:placeholder="'<?= Loc::getMessage("ITEM_ROW"); ?> ' + (key + 1)"
                       v-model="fields[key]">
                <div v-if="products[val]" class="name">{{ getName(val) }}</div>
            </div>
        </div>

    </div>
<?else:?>
    <h2><?= Loc::getMessage("FOR_AUTH"); ?></h2>
<?endif;?>

<script>
    var componentData = <?= CUtil::PHPToJSObject($componentData); ?>;

    BX.message({
        SUCCESS:'<?= Loc::getMessage("SUCCESS"); ?>',
        NOT_FOUND:'<?= Loc::getMessage("NOT_FOUND"); ?>',
        ERR:'<?= Loc::getMessage("ERR"); ?>'
    });
</script>
