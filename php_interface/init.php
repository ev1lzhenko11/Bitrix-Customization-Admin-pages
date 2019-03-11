<?
AddEventHandler("main", "OnEndBufferContent", "removeType");

function removeType(&$content)
{
    $content = replace_output($content);
}
function replace_output($d)
{
    if(CSite::InDir('/bitrix/admin/highloadblock_rows_list.php')){
        $MY_HL_BLOCK_ID = $_GET["ENTITY_ID"];
        CModule::IncludeModule('highloadblock');
        $entity_data_class = GetEntityDataClass($MY_HL_BLOCK_ID);
        if($entity_data_class == "\MedicalAnketsTable"){
            $rsData = $entity_data_class::getList(array(
                'select' => array('*')
            ));

            $uniqueItems = [];
            $CassaAndStore = [];
            while($el = $rsData->fetch()){
                $elements[] = $el;
                if(!in_array($el["UF_ITN"], $uniqueItems)){
                    $uniqueItems[] = $el["UF_ITN"];
                }

                if(mb_strtolower($el["UF_CASSA"]) == "да" and mb_strtolower($el["UF_STORE"]) == "да"){
                    array_push($CassaAndStore, $el);
                }
            }

            $itemCount = count($elements);
            $countUniqueItems = count($uniqueItems);
            $countCassaAndStore = count($CassaAndStore);

            require_once $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/PHPQuery/phpQuery/phpQuery.php';

            $pq = phpQuery::newDocument($content);
            $anketStats = '
            <div class = "adm-filter-content adm-filter-content-table-wrap" style = "max-width:630px; margin-bottom: 20px; border-radius: 5px;padding-top:10px;padding-bottom:10px;">
                <div class="adm-filter-item-left">Количество скачиваний шаблона: 10</div>
                <div class="adm-filter-item-left">Количество заявок: '.$countUniqueItems.'</div>
                <div class="adm-filter-item-left">Количество записей: '.$itemCount.'</div>
                <div class="adm-filter-item-left">Количество записей (касса + склад): '.$countCassaAndStore.'</div>
            </div>
            ';

            $pq->find("#adm-filter-tab-wrap-tbl_med_ankets_filter_id")->prepend($anketStats);
            $content = $pq->html();
        }

    }

    return $content;
}
?>