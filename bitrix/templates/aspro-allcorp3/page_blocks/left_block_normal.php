<?php global $sMenuContent, $isCabinet; ?>
<div class="left_block">
    <div class="sticky-block sticky-block--show-<?=CAllcorp3::GetFrontParametrValue('STICKY_SIDEBAR')?>">
        <?php if ($isCabinet): ?>
            <?php $APPLICATION->IncludeComponent(
                "bitrix:menu",
                "left",
                [
                    "ROOT_MENU_TYPE"        => "cabinet",
                    "MENU_CACHE_TYPE"       => "A",
                    "MENU_CACHE_TIME"       => "3600000",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS"   => [
                    ],
                    "MAX_LEVEL"             => "4",
                    "CHILD_MENU_TYPE"       => "left",
                    "USE_EXT"               => "Y",
                    "DELAY"                 => "N",
                    "ALLOW_MULTI_SELECT"    => "Y",
                    "COMPONENT_TEMPLATE"    => "left",
                ],
                false
            ); ?>
        <?php else: ?>
            <?=$sMenuContent?>
        <?php endif; ?>
        <div class="sidearea">
            <?php $APPLICATION->ShowViewContent('under_sidebar_content'); ?>
            <?php CAllcorp3::get_banners_position('SIDE'); ?>
            <div class="include">
                <?php $APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    ["AREA_FILE_SHOW" => "sect", "AREA_FILE_SUFFIX" => "sidebar", "AREA_FILE_RECURSIVE" => "Y"],
                    false
                ); ?>
            </div>
        </div>
    </div>
</div>