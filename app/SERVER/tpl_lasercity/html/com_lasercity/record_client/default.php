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

if(empty($user_id) || empty($record) || !empty($organization_id)){
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
) as `location`

              FROM `#__lasercity_by_services` as s  
              LEFT JOIN `#__lasercity_affiliates` as a ON s.affiliate_id=a.id
              LEFT JOIN `#__lasercity_translations_" . $lang_tag . "` as t ON a.id=t.object_id 
              WHERE s.user_id='" . (int)$user_id . "' 
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
    $affiliate_id = array();
    $raiting_affiliate_id = array();

    //узнаем прайсы филиала
    foreach ($result as $row) {
        $row->date_added = date('H:i', strtotime($row->date_added));
        $services[] = $row;

        $affiliate_id[$row->affiliate_id] = ContentLoader::getRaiting($row->affiliate_id);

        $raiting_affiliate_id[$row->affiliate_id] = ContentLoader::getRaiting($row->user_id,'user_id');

        //$apparat_id[] = implode(",",json_decode($row->apparat_id,true));
        $service_id[] = implode(",",json_decode($row->service_id,true));
        $prices_id[] = implode(",",json_decode($row->prices_id,true));
        //$service_count_values[] = implode(",",json_decode($row->service_id,true));
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
    $services_dopol['raiting_array'] = $affiliate_id;
    $services_dopol['raiting_array_by_user'] = $raiting_affiliate_id;

    /*echo "<pre>";
    print_r($services);
    print_r($services_dopol);
    //print_r($service_count_values);
    echo "</pre>";*/

}
if(empty($services)){
    header("Location: ".$current_sef."records-client/");
    exit();
}

?>

<main class="counter-by-held-client">
    <div class="record-client__wrapper">
        <nav class="record-client__nav">
            <a class="button-back" href="<?=$current_sef."records-client/?status=0"?>"><?=JText::_('COM_LASERCITY_RECORD_BACK_APPLICATION')?></a>
        </nav>
        <nav class="record-client__nav">
            <a class="button-back" href="<?=$current_sef."records-client/?status=1"?>"><?=JText::_('COM_LASERCITY_RECORD_BACK_SOGLASS')?></a>
        </nav>
        <nav class="record-client__nav">
            <a class="button-back" href="<?=$current_sef."records-client/?status=2"?>"><?=JText::_('COM_LASERCITY_RECORD_BACK_SOST')?></a>
        </nav>

        <section class="record-client record-client--held" aria-labelledby="recordClient">
            <h1 class="visually-hidden" id="recordClient"><?=JText::_('COM_LASERCITY_RECORD_CLIENT_APPLICATION')?></h1>
            <form method="post" class="recording__form__services in-client-services">
                <?php foreach ($services as $service): ?>
                <input type="hidden" name="record_id" value="<?=$service->id?>">
                <input type="hidden" name="records_id[]" value="<?=$service->id?>">
                <div class="record-client__salon-wrapper">
                    <div class="record-client__salon record-client__salon--<?=(!empty($service->stock_id) ? 'byaction' : 'bysalon')?>">
                        <time datetime="<?=date('Y',strtotime($service->date_added))?>"><?=$service->date_added?></time>
                        <span class="record-client__applications-salon-name"><?=$service->value?></span>
                        <div class="record-client__salon-rating rating-salon">
                            <div class="rating-salon__point-wrapper">
                                <output class="rating-salon__point"><?=$services_dopol['raiting_array'][$service->affiliate_id]['raiting']?></output>
                                <ul class="rating-salon-stars">
                                    <li class="rating-salon__star rating-salon__star--silver">
                                        <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                                            <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                                        </svg>
                                        <span class="visually-hidden">Хорошо</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <button class="record-client__applications-salon-contact" title="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_WRITE_SALON')?>" aria-label="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_WRITE_SALON')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDS_CLIENT_WRITE_SALON')?></span><span><?=JText::_('COM_LASERCITY_RECORDS_CLIENT_WRITE')?></span></button>
                    </div>
                    <div class="record-client__salon-option">
                        <?php  if(!empty((int)$services_dopol['raiting_array'][$service->affiliate_id]['raiting'])): $yes_review = 1; ?>
                            <div class="record-client__salon-option-review"><?=JText::_('COM_LASERCITY_RECORDS_CLIENT_OCENENO')?>
                                <div class="record-client__salon-point rating-salon">
                                    <div class="rating-salon__point-wrapper">
                                        <output class="rating-salon__point"><?=$services_dopol['raiting_array'][$service->affiliate_id]['raiting']?></output>
                                        <ul class="rating-salon-stars">
                                            <li class="rating-salon__star rating-salon__star--silver">
                                                <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                                                    <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                                                </svg>
                                                <span class="visually-hidden">Хорошо</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <button class="record-client__salon-delete button button--gray delete-record" data-delete="<?=$service->id?>" title="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?>" aria-label="<?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDS_CLIENT_DELETE')?></span></button>
                        <?php else: ?>
                            <span class="record-client__salon-option-noreview"><?=JText::_('COM_LASERCITY_RECORDS_CLIENT_OZIDAE')?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="record-client__info-wrapper">
                    <div class="record-client__salon-info">
                        <p class="record-client__salon-address">
                            <?php if (!empty($service->metro_line)): ?>
                                <svg fill="<?= $service->metro_line ?>" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="337.5 232.3 125 85.9" aria-hidden="true">
                                     <polygon points="453.9,306.2 424.7,232.3 400,275.5 375.4,232.3 346.1,306.2 337.5,306.2 337.5,317.4 381.7,317.4 381.7,306.2 375.1,306.2 381.5,287.8 400,318.2 418.5,287.8 424.9,306.2 418.3,306.2 418.3,317.4 462.5,317.4 462.5,306.2 "></polygon>
                                </svg>
                            <?php endif; ?>
                            <?=$service->address.", ".$service->district.", ".$service->location?>
                        </p>
                        <span class="record-client__salon-phone"><?=str_replace(array('[',']','"',','),array("","",""," , "),$service->phones)?></span>
                        <a class="record-client__salon-map button-onmap" href="https://www.google.com/maps?q=<?= $service->coordinate ?>" target="_blank" rel="noreferrer noopener"><?=JText::_('COM_LASERCITY_ALL_TXT_MAP_GOOGLE')?></a>
                    </div>
                    <p class="record-client__comment"><?=$service->koment?></p>
                    <ul class="record-client__procedures">
                        <?php $all_price=0; foreach ($services_dopol['service_name'] as $service_name):

                            $sex = $service_name['persones']==0 ? '/templates/lasercity/img/woman.svg' : '/templates/lasercity/img/man.svg';

                            $all_price += $service_name['price_digit'];
                            ?>
                            <li class="record-client__procedure" style="background: url('<?=$sex?>') no-repeat left center;background-size: 20px;padding-left: 25px">
                                <span class="record-client__procedure-time"><?=$service_name['duration']?></span>
                                <span class="record-client__procedure-zona"><?=$service_name['service']?></span>
                                <span class="record-client__procedure-price"><?=$service_name['price']?></span>
                                <span class="record-client__procedure-apparatus"><?=$service_name['apparat_name']?></span>
                            </li>
                        <?php endforeach;?>
                    </ul>
                    <div class="record-client__topay-wrapper topay">
                        <?=JText::_('COM_LASERCITY_RECORDING_PRICE')?>:
                        <ul class="topay__money-list">
                            <li class="topay__money-item topay__money-item--card" title="<?=JText::_('COM_LASERCITY_RECORDING_BY_CREDI_CARD')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDING_BY_CREDI_CARD')?></span></li>
                            <li class="topay__money-item topay__money-item--cash" title="<?=JText::_('COM_LASERCITY_RECORDING_BY_NALOM')?>"><span class="visually-hidden"><?=JText::_('COM_LASERCITY_RECORDING_BY_NALOM')?></span></li>
                        </ul>
                        <output><?=$all_price?> грн</output>
                    </div>
                    <div class="record-client__wish-info select-date">
                        <p class="select-date__wish-day"><?=JText::_('COM_LASERCITY_RECORDING_YOU_DATA')?>
                            <span>
                                <!--<output>30.11.2018</output>-->
                                <input readonly type="text" id="send_date_uslug" name="date_visit" value="<?=date('d.m.Y',strtotime($service->date_visit))?>">
                                <button class="select-date__wish-button" type="button" title="" aria-label=""></button>
                             </span>
                        </p>
                        <p class="select-date__wish-time">
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_TIME')?><br>
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_OT')?> <span class="select-date__wish-time--from"><label><input readonly name="hour1" type="text" value="<?=date('H',strtotime($service->date_visit))?>"></label> : <label><input readonly name="minut1" type="text" value="<?=date('i',strtotime($service->date_visit))?>"></label></span>
                            <?=JText::_('COM_LASERCITY_RECORDING_YOU_DO')?> <span class="select-date__wish-time--until"><label><input readonly name="hour2" type="text" value="<?=date('H',strtotime($service->date_end))?>"></label> : <label><input readonly name="minut2" type="text" value="<?=date('i',strtotime($service->date_end))?>"></label></span>
                        </p>
                    </div>
                </div>
                <p class="record-client__warning">
                    <?php
                        switch ($service->status){
                            case 0: echo JText::_('COM_LASERCITY_RECORD_CLIENT_YOU_APPLICATION_CHECK');
                                break;
                            case 1: echo JText::_('COM_LASERCITY_RECORD_CLIENT_YOU_APPLICATION_SUCCESS');
                                    if(empty($yes_review)){echo ' '.'После прохождения процедуры обязательно оставьте отзыв!';}
                                break;
                            case 3: if(empty($yes_review)){echo 'После прохождения процедуры обязательно оставьте отзыв!';}
                                break;
                        }
                    ?>
                </p>
                <?php endforeach;?>
            </form>
        </section>










        <!--<section class="record-client record-client--new" aria-labelledby="recordClient">
            <h1 class="visually-hidden" id="recordClient">Заявка поситителя</h1>
            <form method="post">
                <div class="record-client__salon record-client__salon--byaction">
                    <time datetime="2017">8:35</time>
                    <span class="record-client__applications-salon-name">Люменис в Дарницком районе</span>
                    <div class="record-client__salon-rating rating-salon">
                        <div class="rating-salon__point-wrapper">
                            <output class="rating-salon__point">4,9</output>
                            <ul class="rating-salon-stars">
                                <li class="rating-salon__star rating-salon__star--silver">
                                    <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                                        <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                                    </svg>
                                    <span class="visually-hidden">Хорошо</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <button class="record-client__applications-salon-contact" title="Написать в салон" aria-label="Написать в салон"><span class="visually-hidden">Написать в салон</span><span>Написать</span></button>
                </div>
                <div class="record-client__info-wrapper">
                    <div class="record-client__salon-info">
                        <p class="record-client__salon-address">
                            <svg fill="red" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="337.5 232.3 125 85.9" aria-hidden="true">
                                <polygon points="453.9,306.2 424.7,232.3 400,275.5 375.4,232.3 346.1,306.2 337.5,306.2 337.5,317.4 381.7,317.4 381.7,306.2 375.1,306.2 381.5,287.8 400,318.2 418.5,287.8 424.9,306.2 418.3,306.2 418.3,317.4 462.5,317.4 462.5,306.2 "></polygon>
                            </svg>
                            Дарница ул. Жмаченко, Дарница, Новая Дарница
                        </p>
                        <span class="record-client__salon-phone">(063) 568-29-24</span>
                        <a class="record-client__salon-map button-onmap" href="#" target="_blank" rel="noreferrer noopener">На карте Google</a>
                    </div>
                    <p class="record-client__comment">Ребята буду спешить, если по графику не буду успевать, то лучше наберите выберем другое время</p>
                    <ul class="record-client__procedures">
                        <li class="record-client__procedure">
                            <span class="record-client__procedure-time">10 мин</span>
                            <span class="record-client__procedure-zona">Белая линия живота</span>
                            <span class="record-client__procedure-price">320 грн</span>
                            <span class="record-client__procedure-apparatus">Lumenis LightSheer Desire</span>
                        </li>
                    </ul>
                    <div class="record-client__topay-wrapper topay">
                        К оплате:
                        <ul class="topay__money-list">
                            <li class="topay__money-item topay__money-item--card" title="Оплата с  помощью крети. карты"><span class="visually-hidden">Оплата с  помощью крети. карты</span></li>
                            <li class="topay__money-item topay__money-item--cash" title="Оплата с  помощью наличк"><span class="visually-hidden">Оплата с  помощью налички</span></li>
                        </ul>
                        <output>2220 грн</output>
                    </div>
                    <div class="record-client__wish-info select-date">
                        <p class="select-date__wish-day">Желаемое время
                            <span><output>30.11.2018</output>
              <button class="select-date__wish-button" type="button" title="Выбрать желаемую дату" aria-label="Выбрать желаемую дату"></button>
             </span>
                        </p>
                        <p class="select-date__wish-time">
                            Желаемое время<br>
                            от <span class="select-date__wish-time--from"><label><input type="text"></label> : <label><input type="text"></label></span>
                            до <span class="select-date__wish-time--until"><label><input type="text"></label> : <label><input type="text"></label></span>
                        </p>
                        <div class="select-date__calendar calendar">
                            <output class="calendar__output">15.05.2019</output>
                            <p class="calendar__wrapper">
                                <select aria-label="Выбор месяца">
                                    <option>2019</option>
                                </select>
                                <select aria-label="Выбор года">
                                    <option>сентябрь</option>
                                </select>
                            </p>
                            <table class="calendar__table">
                                <thead>
                                <tr>
                                    <td>Пн</td>
                                    <td>Вт</td>
                                    <td>Ср</td>
                                    <td>Чт</td>
                                    <td>Пт</td>
                                    <td>Сб</td>
                                    <td>Вс</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <button type="button">1</button>
                                    </td>
                                    <td>
                                        <button type="button">2</button>
                                    </td>
                                    <td>
                                        <button type="button">3</button>
                                    </td>
                                    <td>
                                        <button type="button">4</button>
                                    </td>
                                    <td>
                                        <button type="button">5</button>
                                    </td>
                                    <td>
                                        <button type="button">6</button>
                                    </td>
                                    <td>
                                        <button type="button">7</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button">8</button>
                                    </td>
                                    <td>
                                        <button type="button">9</button>
                                    </td>
                                    <td>
                                        <button type="button">10</button>
                                    </td>
                                    <td>
                                        <button type="button">11</button>
                                    </td>
                                    <td>
                                        <button type="button">12</button>
                                    </td>
                                    <td>
                                        <button type="button">13</button>
                                    </td>
                                    <td class="calendar__active-date">
                                        <button type="button">14</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button">15</button>
                                    </td>
                                    <td>
                                        <button type="button">16</button>
                                    </td>
                                    <td>
                                        <button type="button">17</button>
                                    </td>
                                    <td>
                                        <button type="button">18</button>
                                    </td>
                                    <td>
                                        <button type="button">19</button>
                                    </td>
                                    <td>
                                        <button type="button">20</button>
                                    </td>
                                    <td>
                                        <button type="button">21</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button">22</button>
                                    </td>
                                    <td>
                                        <button type="button">23</button>
                                    </td>
                                    <td>
                                        <button type="button">24</button>
                                    </td>
                                    <td>
                                        <button type="button">25</button>
                                    </td>
                                    <td>
                                        <button type="button">26</button>
                                    </td>
                                    <td>
                                        <button type="button">27</button>
                                    </td>
                                    <td>
                                        <button type="button">28</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <p class="record-client__warning">Ваша заявка принята в работу ожидайте в скором времени Вам позвонит администротор для согласования даты!</p>
            </form>
        </section>

        <section class="record-client record-client--agreed" aria-labelledby="recordClient">
            <h1 class="visually-hidden" id="recordClient">Заявка поситителя</h1>
            <form method="post">
                <div class="record-client__salon record-client__salon--byaction">
                    <time datetime="2017">8:35</time>
                    <span class="record-client__applications-salon-name">Люменис в Дарницком районе</span>
                    <div class="record-client__salon-rating rating-salon">
                        <div class="rating-salon__point-wrapper">
                            <output class="rating-salon__point">4,9</output>
                            <ul class="rating-salon-stars">
                                <li class="rating-salon__star rating-salon__star--silver">
                                    <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207" aria-hidden="true">
                                        <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
                                    </svg>
                                    <span class="visually-hidden">Хорошо</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <button class="record-client__applications-salon-contact" title="Написать в салон" aria-label="Написать в салон"><span class="visually-hidden">Написать в салон</span><span>Написать</span></button>
                </div>
                <div class="record-client__info-wrapper">
                    <div class="record-client__salon-info">
                        <p class="record-client__salon-address">
                            <svg fill="red" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="337.5 232.3 125 85.9" aria-hidden="true">
                                <polygon points="453.9,306.2 424.7,232.3 400,275.5 375.4,232.3 346.1,306.2 337.5,306.2 337.5,317.4 381.7,317.4 381.7,306.2 375.1,306.2 381.5,287.8 400,318.2 418.5,287.8 424.9,306.2 418.3,306.2 418.3,317.4 462.5,317.4 462.5,306.2 "></polygon>
                            </svg>
                            Дарница ул. Жмаченко, Дарница, Новая Дарница
                        </p>
                        <span class="record-client__salon-phone">(063) 568-29-24</span>
                        <a class="record-client__salon-map button-onmap" href="#">На карте Google</a>
                    </div>
                    <p class="record-client__comment">Ребята буду спешить, если по графику не буду успевать, то лучше наберите выберем другое время</p>
                    <ul class="record-client__procedures">
                        <li class="record-client__procedure">
                            <span class="record-client__procedure-time">10 мин</span>
                            <span class="record-client__procedure-zona">Белая линия живота</span>
                            <span class="record-client__procedure-price">320 грн</span>
                            <span class="record-client__procedure-apparatus">Lumenis LightSheer Desire</span>
                        </li>
                    </ul>
                    <div class="record-client__topay-wrapper topay">
                        К оплате:
                        <ul class="topay__money-list">
                            <li class="topay__money-item topay__money-item--card" title="Оплата с  помощью крети. карты"><span class="visually-hidden">Оплата с  помощью крети. карты</span></li>
                            <li class="topay__money-item topay__money-item--cash" title="Оплата с  помощью наличк"><span class="visually-hidden">Оплата с  помощью налички</span></li>
                        </ul>
                        <output>2220 грн</output>
                    </div>
                    <div class="record-client__wish-info select-date">
                        <p class="select-date__wish-day">Желаемое время
                            <span><output>30.11.2018</output>
              <button class="select-date__wish-button" type="button" title="Выбрать желаемую дату" aria-label="Выбрать желаемую дату"></button>
             </span>
                        </p>
                        <p class="select-date__wish-time">
                            Желаемое время<br>
                            от <span class="select-date__wish-time--from"><label><input type="text"></label> : <label><input type="text"></label></span>
                            до <span class="select-date__wish-time--until"><label><input type="text"></label> : <label><input type="text"></label></span>
                        </p>
                        <div class="select-date__calendar calendar">
                            <output class="calendar__output">15.05.2019</output>
                            <p class="calendar__wrapper">
                                <select aria-label="Выбор месяца">
                                    <option>2019</option>
                                </select>
                                <select aria-label="Выбор года">
                                    <option>сентябрь</option>
                                </select>
                            </p>
                            <table class="calendar__table">
                                <thead>
                                <tr>
                                    <td>Пн</td>
                                    <td>Вт</td>
                                    <td>Ср</td>
                                    <td>Чт</td>
                                    <td>Пт</td>
                                    <td>Сб</td>
                                    <td>Вс</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <button type="button">1</button>
                                    </td>
                                    <td>
                                        <button type="button">2</button>
                                    </td>
                                    <td>
                                        <button type="button">3</button>
                                    </td>
                                    <td>
                                        <button type="button">4</button>
                                    </td>
                                    <td>
                                        <button type="button">5</button>
                                    </td>
                                    <td>
                                        <button type="button">6</button>
                                    </td>
                                    <td>
                                        <button type="button">7</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button">8</button>
                                    </td>
                                    <td>
                                        <button type="button">9</button>
                                    </td>
                                    <td>
                                        <button type="button">10</button>
                                    </td>
                                    <td>
                                        <button type="button">11</button>
                                    </td>
                                    <td>
                                        <button type="button">12</button>
                                    </td>
                                    <td>
                                        <button type="button">13</button>
                                    </td>
                                    <td class="calendar__active-date">
                                        <button type="button">14</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button">15</button>
                                    </td>
                                    <td>
                                        <button type="button">16</button>
                                    </td>
                                    <td>
                                        <button type="button">17</button>
                                    </td>
                                    <td>
                                        <button type="button">18</button>
                                    </td>
                                    <td>
                                        <button type="button">19</button>
                                    </td>
                                    <td>
                                        <button type="button">20</button>
                                    </td>
                                    <td>
                                        <button type="button">21</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button">22</button>
                                    </td>
                                    <td>
                                        <button type="button">23</button>
                                    </td>
                                    <td>
                                        <button type="button">24</button>
                                    </td>
                                    <td>
                                        <button type="button">25</button>
                                    </td>
                                    <td>
                                        <button type="button">26</button>
                                    </td>
                                    <td>
                                        <button type="button">27</button>
                                    </td>
                                    <td>
                                        <button type="button">28</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <p class="record-client__warning">Дата согласована! Салон ожидает Вас на процедуру! После прохождения процедуры обязательно оставьте отзыв!</p>
            </form>
        </section>-->

    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        /*$(document).ready(function () {
            $("#send_date_uslug").MyDtime({language: '<?=LangHelper::getCurrentSef()?>'});
        });*/
    });
</script>