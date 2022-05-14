<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/**
 * Шаблон для вывода кол-ва товаров
 */
/** @var array $arResult */
/** @var CBitrixComponent $component */
/** @var CMain $APPLICATION */
/** @var $arParams */

$class = '';
if(!$arResult['PRODUCTS_ID']){
    $class = 'd-none';
}

?>
<div class="<?= $class; ?> d-lg-flex h-100 comparsion_quantity_container">
    <a href="/personal/wishlist/" class="header-main__comparsion h-100 px-3 d-flex align-items-center">
    <span class="position-relative">
	      <i class="icn"></i>
	      <span class="position-absolute start-100 translate-middle badge rounded-circle bg-secondary js-comparsion_quantity"
                id="header_comparsion_quantity">
              <?= count($arResult['PRODUCTS_ID']); ?>
          </span>
    </span>
    </a>
</div>
