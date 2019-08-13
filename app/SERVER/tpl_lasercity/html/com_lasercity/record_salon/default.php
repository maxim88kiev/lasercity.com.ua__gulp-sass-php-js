<?php
// опеределяем язык страницы
$db = JFactory::getDbo();
$lang_tag = JFactory::getLanguage()->getTag();

//узнаем ссылку на язык
$default_sef = "";
$current_sef = LangHelper::getCurrentSef();
$language_default = JComponentHelper::getParams('com_languages')->get('site');
$db = JFactory::getDbo();
$db->setQuery("SELECT sef FROM `#__languages` WHERE lang_code = '".$language_default."' ");
$result = $db->loadObjectList();
foreach ($result as $row) {
    if (!empty($row->sef)) {$default_sef = $row->sef; break;}
}
$current_sef = str_replace($default_sef,"",$current_sef);
$current_sef = empty($current_sef) ? "/" : "/".$current_sef."/";

$user_id = JFactory::getSession()->get('my_user_id');
$tip_user = JFactory::getSession()->get('tip_user');
$organization_id = JFactory::getSession()->get('organization_id');


$record = JFactory::getApplication()->input->getString('record');

if(empty($user_id) || empty($organization_id)){
    header("Location: ".$current_sef);
    exit();
}else{

    $query = "SELECT s.*,t.value,a.alias as affiliate_alias,a.phones,a.coordinate,a.address,

(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='district' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=a.district
) as `district`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='micro_district' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=a.micro_district
) as `micro_district`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='metro' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=a.metro
) as `metro`,
(SELECT `line` FROM `#__lasercity_metro` as `t` WHERE 
    `t`.`id`=a.metro
) as `metro_line`,
(SELECT `value` FROM `#__lasercity_translations_{$lang_tag}` as `t` WHERE 
    `t`.`object_name`='location' AND 
    `t`.`object_column`='title' AND 
    `t`.`object_id`=a.location
) as `location`,

                (SELECT t.value  
                    FROM `#__lasercity_organizations` as o 
                    LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON o.id=t.object_id 
                    WHERE o.id='$organization_id'
                    AND t.object_column='title' 
                    AND t.object_name='organization') as name_organization, 
                    
              (SELECT CONCAT_WS(',',u.name,d.phone) 
                    FROM `#__users` as u  
                    LEFT JOIN `#__users_description` as d ON u.id=d.user_id 
                    WHERE u.id=s.user_id) as users_data

              FROM `#__lasercity_by_services` as s  
              LEFT JOIN `#__lasercity_affiliates` as a ON s.affiliate_id=a.id 
              LEFT JOIN `#__lasercity_organizations` as o ON a.organization=o.id 
              LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON a.id=t.object_id 
              WHERE o.id='" . (int)$organization_id . "' 
              AND s.id='$record' 
              AND s.arhiv='0'
              AND t.object_column='title' 
              AND t.object_name='affiliate' 
              ORDER BY date_added DESC";

    $db->setQuery($query);
    $result = $db->loadObjectList();

    $services = array();
    //$apparat_id = array();
    $service_id = array();
    $prices_id = array();
    //$service_count_values = array();
    //$affiliate_id = array();
    //$raiting_affiliate_id = array();

    //узнаем прайсы филиала
    foreach ($result as $row) {
        $row->date_added = date('H:i', strtotime($row->date_added));
        $row->date_agreed = $row->date_agreed!='0000-00-00 00:00:00' ? $row->date_agreed : $row->date_visit;

        $row->name = empty($row->name) ? explode(",",$row->users_data)[0] : $row->name;
        $row->phone = str_replace("+38 (0__) ___ __ __","",$row->phone);

        $kontakt = empty(explode(",",$row->users_data)[1]) ? $row->email : explode(",",$row->users_data)[1];
        $row->phone = empty($row->phone) ? $kontakt : $row->phone;

        $services[] = $row;

        //$affiliate_id[$row->affiliate_id] = ContentLoader::getRaiting($row->affiliate_id);

        //$raiting_affiliate_id[$row->affiliate_id] = ContentLoader::getRaiting($row->user_id,'user_id');

        //$apparat_id[] = implode(",",json_decode($row->apparat_id,true));
        $service_id[] = implode(",",json_decode($row->service_id,true));
        $prices_id[] = implode(",",json_decode($row->prices_id,true));
        //$service_count_values[] = implode(",",json_decode($row->service_id,true));
        $name_organization = $row->name_organization;
    }

    //$service_count_values = array_count_values(explode(",",implode(",",$service_count_values)));
    //$apparat_id = explode(",",implode(",",$apparat_id));
    $prices_id = explode(",",implode(",",$prices_id));
    $service_id = explode(",",implode(",",$service_id));

    /*echo "<pre>";
    print_r($prices_id);
    print_r($service_id);
    echo "</pre>";*/

    //$services_dopol['apparat_name'] = ContentLoader::getAparatsByArray($apparat_id);
    $services_dopol['service_name'] = ContentLoader::getServisesByPrice($prices_id,$service_id);
    //$services_dopol['raiting_array'] = $affiliate_id;
    //$services_dopol['raiting_array_by_user'] = $raiting_affiliate_id;

    /*echo "<pre>";
    //print_r($services);
    print_r($services_dopol);
    //print_r($service_count_values);
    echo "</pre>";*/

}
if(empty($services)){
    header("Location: ".$current_sef."records-salon/");
    exit();
}

?>
<main class="counter-by-held-salon">
    <div class="record-salon__wrapper">
        <nav class="record-salon__nav">
            <a class="button-back" href="<?=$current_sef."records-salon/?status=0"?>"><?=JText::_('COM_LASERCITY_RECORD_BACK_APPLICATION')?></a>
        </nav>
        <nav class="record-salon__nav">
            <a class="button-back" href="<?=$current_sef."records-salon/?status=1"?>"><?=JText::_('COM_LASERCITY_RECORD_BACK_SOGLASS')?></a>
        </nav>
        <nav class="record-salon__nav">
            <a class="button-back" href="<?=$current_sef."records-salon/?status=2"?>"><?=JText::_('COM_LASERCITY_RECORD_BACK_SOST')?></a>
        </nav>

        <section class="record-salon record-salon--held" aria-labelledby="recordSalon">
            <h1 class="visually-hidden" id="recordSalon">Заявка поситителя</h1>
            <form method="post" id="record_form_by_select" class="recording__form__services in-client-services">
                <?php foreach ($services as $service): ?>
                <div class="record-salon__client record-salon__client--<?=(!empty($service->stock_id) ? 'byaction' : 'bysalon')?>">
                    <time datetime="<?=date('Y',strtotime($service->date_added))?>"><?=$service->date_added?></time>
                    <p class="record-salon__applications-client-wrapper">
                        <span class="record-salon__applications-client-name"><?=$service->name?></span>
                        <a href="#" class="record-salon__applications-client-number"><?=$service->phone?></a>
                    </p>
                    <button class="record-salon__applications-client-contact" title="Написать клиенту" aria-label="Написать клиенту"><span class="visually-hidden">Написать клиенту</span><span>Написать</span></button>
                </div>
                <div class="record-salon__info-wrapper">
                    <p class="record-salon__comment">
                        <?=$service->koment?>
                    </p>
                    <p class="record-salon__address">
                        <?=$service->value?><br>
                        <span class="record-salon__street"><?=$service->address?></span>
                    </p>
                    <input type="hidden" name="record_id" value="<?=$service->id?>">
                    <input type="hidden" name="records_id[]" value="<?=$service->id?>">
                    <ul class="record-salon__procedures conteiner-recording">
                        <?php $all_price=0; foreach ($services_dopol['service_name'] as $service_name):

                            $sex = $service_name['persones']==0 ? 'item-salon__procedure--woman' : 'item-salon__procedure--man';

                            $all_price += $service_name['price_digit'];
                            ?>
                            <li class="record-salon__procedure <?=$sex?>">

                                <input type="hidden" name="sex_id[]" value="<?=$service_name['persones']?>">
                                <input type="hidden" name="apparat_id[]" value="<?=$service_name['apparat']?>">
                                <input type="hidden" name="service_id[]" value="<?=$service_name['service_id']?>">
                                <input type="hidden" name="prices_id[]" value="<?=$service_name['prices_id']?>">
                                <input type="hidden" name="price[]" class="price-element" value="<?=$service_name['price_digit']?>">
                                <input type="hidden" name="duration[]" class="time-element" value="<?=$service_name['duration']?>">

                                <span class="record-salon__procedure-time"><?=$service_name['duration']?></span>
                                <span class="record-salon__procedure-zona"><?=$service_name['service']?></span>
                                <span class="record-salon__procedure-price"><?=$service_name['price']?></span>
                                <span class="record-salon__procedure-apparatus"><?=$service_name['apparat_name']?></span>
                                <?php if($service->status<2): ?>
                                    <button class="record-salon__procedure-button button-cross" title="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?>" aria-label="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?>" type="button"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?></span></button>
                                <?php endif;?>
                            </li>
                        <?php endforeach;?>
                        <!--<li class="record-salon__procedure">
                            <span class="record-salon__procedure-time">10 мин</span>
                            <span class="record-salon__procedure-zona">Белая линия живота</span>
                            <span class="record-salon__procedure-price">320 грн</span>
                            <span class="record-salon__procedure-apparatus">Lumenis LightSheer Desire</span>
                            <button class="record-salon__procedure-button button-cross" title="Убрать эелемент из списка" aria-label="Убрать эелемент из списка" type="button"><span class="visually-hidden">Убрать эелемент из списка</span></button>
                        </li>-->
                    </ul>
                    <div class="record-salon__checkout">
                        <div class="record-salon__topay-wrapper topay">
                            <?=JText::_('COM_LASERCITY_RECORDING_PRICE')?>:
                            <ul class="topay__money-list">
                                <li class="topay__money-item topay__money-item--card" title="<?=JText::_('COM_LASERCITY_RECORDING_BY_CREDI_CARD')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDING_BY_CREDI_CARD')?></span></li>
                                <li class="topay__money-item topay__money-item--cash" title="<?=JText::_('COM_LASERCITY_RECORDING_BY_NALOM')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDING_BY_NALOM')?></span></li>
                            </ul>
                            <output class="popup-recording__pay"><?=$all_price?> грн</output>
                        </div>
                        <input name="affiliate" type="hidden" value="<?=$service->affiliate_id?>">
                        <?php if($service->status<2): ?>
                            <div class="order__services-input-wrapper">
                                <input name="stock" type="hidden" value="<?=$service->stock_id?>">
                                <input name="service_txt" id="input_service" class="order__services-input" type="search" autocomplete="off" aria-label="<?=JText::_('COM_LASERCITY_RECORDING_NAME_USLG')?>" placeholder="<?=JText::_('COM_LASERCITY_RECORDING_NAME_USLG')?>">
                                <button class="order__services-input-button" type="button" aria-label="" title=""><span class="visually-hidden"></span></button>
                            </div>
                            <a class="record-salon__add-procedure button-addprocedure" href="#"><?=JText::_('COM_LASERCITY_RECORDING_ADD_PROCED')?></a>
                        <?php endif;?>
                    </div>
                    <div class="record-salon__wish-info select-date">
                        <p class="select-date__wish-day"><?=JText::_('COM_LASERCITY_RECORDING_YOU_DATA')?>
                            <span>
                                <output><?=date('d.m.Y',strtotime($service->date_visit))?></output>
                             </span>
                        </p>
                        <p class="select-date__wish-time">
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_TIME')?><br>
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_OT')?> <span class="select-date__wish-time--from"><label><input readonly type="text" value="<?=date('H',strtotime($service->date_visit))?>"></label> : <label><input readonly type="text" value="<?=date('i',strtotime($service->date_visit))?>"></label></span>
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_DO')?> <span class="select-date__wish-time--until"><label><input readonly type="text" value="<?=date('H',strtotime($service->date_end))?>"></label> : <label><input readonly type="text" value="<?=date('i',strtotime($service->date_end))?>"></label></span>
                        </p>
                    </div>
                    <div class="record-salon__wish-info select-date">
                        <p class="select-date__wish-day">Согласованная дата
                            <span>
                                <!--<output>30.11.2018</output>-->
                                <input type="text" id="send_date_uslug" name="date_visit" value="<?=date('d.m.Y',strtotime($service->date_agreed))?>">
                                <button class="select-date__wish-button" type="button" title="" aria-label=""></button>
                             </span>
                        </p>
                        <p class="select-date__wish-time auto-time-do">
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_TIME')?><br>
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_OT')?> <span class="select-date__wish-time--from"><label><input name="hour1" maxlength="2" type="text" value="<?=date('H',strtotime($service->date_agreed))?>"></label> : <label><input name="minut1" maxlength="2" type="text" value="<?=date('i',strtotime($service->date_agreed))?>"></label></span>
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_DO')?> <span class="select-date__wish-time--until"><label><input readonly="readonly" name="hour2" type="text" value="<?=date('H',strtotime($service->date_end))?>"></label> : <label><input readonly="readonly" name="minut2" type="text" value="<?=date('i',strtotime($service->date_end))?>"></label></span>
                        </p>
                    </div>

                        <p class="record-salon__buttons record-salon__buttons--held">
                            <?php if($service->status<=1): ?>
                                <button class="record-salon__button record-salon__button--accept button">Подтвердить</button>
                                <button class="record-salon__button record-salon__button--cancel button button--gray">Отменить</button>
                            <?php elseif($service->status==2): ?>
                                <?php if(empty($service->confirmed)): ?>
                                    <button class="record-salon__button record-salon__button--completed button" data-record_id="<?=$service->id?>">Процедура <br>состоялась</button>
                                    <button class="record-salon__button record-salon__button--abolished button button--gray" data-record_id="<?=$service->id?>">Процедура <br>отменена</button>
                                <?php else: ?>
                                    <p>ПРОЦЕДУРА УСПЕШНО СОСТОЯЛАСЬ</p>
                                <?php endif;?>
                            <?php endif;?>

                            <?php if($service->status>=2): ?>
                                <button class="record-salon__button record-salon__button--trash button button--gray" title="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?>" aria-label="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?>">
                                    <span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?></span>
                                </button>
                            <?php endif;?>
                        </p>
                        <div class="record-salon__held-warning">
                            Вы действительно хотите удалить навсегда?
                            <p class="record-salon__warning-buttons">
                                <button class="record-salon__warning-button record-salon__warning-button--yes button" data-delete="<?=$service->id?>">Да</button>
                                <button class="record-salon__warning-button record-salon__warning-button--no button button--gray">Нет</button>
                            </p>
                        </div>

                </div>
                <?php endforeach;?>
            </form>
        </section>

    </div>
</main>
<style>
    .record-salon.record-salon--held .record-salon__button--trash{
        min-height: 35px
    }
    .record-salon.record-salon--held .record-salon__held-warning{
        display: none;
    }
    .item-salon__procedure--woman{
        background: url('/templates/lasercity/img/woman.svg') no-repeat left center;
        background-size: 20px;
        padding-left: 25px
    }
    .item-salon__procedure--man{
        background: url('/templates/lasercity/img/man.svg') no-repeat left center;
        background-size: 20px;
        padding-left: 25px
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {
            $("#send_date_uslug").MyDtime({language: '<?=LangHelper::getCurrentSef()?>'});
        });
    });
</script>