<?php /*

 Напоминание для ВЛАДА:
 1) не удалять поля type=hidden
 2) не изенять и не удалять классы,id
 3) перед тек как что-то менять уточняй у программистов

 Если слетит функционал ответственность будет на ТЕБЕ

 */ ?>
<?php defined('_JEXEC') or die;
$data = Search::getFiltersList();
$search_block = Search::getInfo()['search_block'];
$count = Search::getInfo()['count'];
function getChecked($block, $alias, $search_block)
{
    return isset($search_block[$block]) ? (in_array($alias, $search_block[$block]) ? 'checked' : null) : null;
} ?>

<div class="multi-popup">
  <div class="multi-popup__navigation-popup">
    <button class="multi-popup__navigation-popup-back button-corner" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_BACK_FIND') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_BACK_FIND') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_FORM_BACK_FIND') ?></span></button>
    <p class="multi-popup__navigation-popup-title">
      <output><?= $count ?></output> <?= JText::_('COM_LASERCITY_ALL_FORM_SALON_ON_MAP') ?></p>
    <button class="multi-popup__navigation-popup-close button-cross" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_FILTER') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_FILTER') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_FILTER') ?></span></button>
  </div>
  <ul class="multi-popup__list">
    <li class="multi-popup__list-item multi-popup__list-item--active">
      <button class="multi-popup__list-button multi-popup__list-button--place" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_OPEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_OPEN') ?>" data-set-modal-value="multiPopupPlace">
        <svg height="23" fill="#c9c9c9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 61.168 61.168">
          <path d="M50.216,37.584h-7.161l3.047-4.4c5.755-7.671,4.922-20.28-1.781-26.982c-3.621-3.622-8.437-5.617-13.56-5.617 c-5.122,0-9.938,1.995-13.56,5.617c-6.703,6.702-7.536,19.312-1.804,26.952l3.068,4.431h-7.513L0,60.584h61.168L50.216,37.584z  M30.938,12.584c3.859,0,7,3.141,7,7s-3.141,7-7,7s-7-3.141-7-7S27.078,12.584,30.938,12.584z M12.216,39.584h7.634l10.911,15.757 l10.91-15.757h7.281l9.048,19H3.168L12.216,39.584z"></path>
        </svg>
          <?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_LOCATION') ?>
      </button>
    </li>
    <li class="multi-popup__list-item">
      <button class="multi-popup__list-button multi-popup__list-button--zona" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_PROCEDURE') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_PROCEDURE') ?>" data-set-modal-value="multiPopupZona">
        <svg height="23" fill="#c9c9c9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92.008 92.008">
          <path d="M46.004,21.672c5.975,0,10.836-4.861,10.836-10.836S51.979,0,46.004,0c-5.975,0-10.835,4.861-10.835,10.836 S40.029,21.672,46.004,21.672z"></path>
          <path d="M68.074,54.008L59.296,26.81c-0.47-1.456-2.036-2.596-3.566-2.596h-1.312H53.48H38.526h-0.938h-1.312 c-1.53,0-3.096,1.14-3.566,2.596l-8.776,27.198c-0.26,0.807-0.152,1.623,0.297,2.24s1.193,0.971,2.041,0.971h2.25 c1.53,0,3.096-1.14,3.566-2.596l2.5-7.75v10.466v0.503v29.166c0,2.757,2.243,5,5,5h0.352c2.757,0,5-2.243,5-5V60.842h2.127v26.166 c0,2.757,2.243,5,5,5h0.352c2.757,0,5-2.243,5-5V57.842v-0.503v-10.47l2.502,7.754c0.47,1.456,2.036,2.596,3.566,2.596h2.25 c0.848,0,1.591-0.354,2.041-0.971S68.334,54.815,68.074,54.008z"></path>
        </svg>
          <?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_TXT') ?>
      </button>
    </li>
    <li class="multi-popup__list-item">
      <button class="multi-popup__list-button multi-popup__list-button--aparat" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_OPEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_OPEN') ?>" data-set-modal-value="multiPopupAparat">
        <svg height="23" fill="#c9c9c9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 372.3 372.3">
          <path d="M300.574,44.227c-5.29-1.985-83.971-31.213-150.086-40.8c-0.707-0.111-1.372-0.169-2.067-0.263 c-3.672-0.718-7.976-1.197-12.71-1.506c-56.155-5.599-95.326,4.77-102.228,4.91c-39.9,0-31.12,127.059-0.403,128.051 c0.753,0.023,1.424,0.052,2.137,0.076c1.196,0.047,4.56,0.193,9.47,0.368c20.295,0.993,24.382,2.896,32.071,12.115 c8.968,10.772,25.042,33.25,25.042,63.162c0,29.904,0,95.74,0,104.721c0,5.324,3.241,8.29,9.447,9.855 c0.128,0.058,0.14,0.116,0.298,0.174c14.526,5.629,8.092,47.211,8.092,47.211h12.868c0,0-5.091-43.17,9.879-46.334 c14.906-0.97,30.069-3.702,29.94-9.984c-0.181-8.35-1.792-66.42,0-105.316c0.607-13.335-1.711-26.086-4.688-37.221h6.458v-16.751 h-11.589c-3.859-11.396-6.709-19.01-4.017-20.592c0.205-0.047,0.456-0.105,0.601-0.152c0.105-0.047,0.216-0.181,0.321-0.251 c38.225-7.812,131.565-33.181,140.651-35.668v0.345l65.496,22.799l0.035-101.672L300.574,44.227z"></path>
        </svg>
          <?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_TXT') ?>
      </button>
    </li>
    <li class="multi-popup__list-item">
      <button class="multi-popup__list-button multi-popup__list-button--comfort" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_KOMFORT_OPEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_KOMFORT_OPEN') ?>" data-set-modal-value="multiPopupComfort">
        <svg height="20" fill="#c9c9c9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 476.164 476.164">
          <path d="M78.736,249.209h64.7c-12.79-25.061-34.581-57.28-71.556-91.496c-7.211-6.673-16.897-10.5-26.577-10.5 c-10.177,0-20.15,3.945-29.643,11.727c-29.601,24.263-9.415,54.24-2.523,62.851c9.107,11.378,18.639,27.408,27.039,46.482 C49.069,256.693,63.042,249.209,78.736,249.209z"></path>
          <path d="M460.503,158.94c-9.493-7.781-19.466-11.727-29.643-11.727c-9.679,0-19.366,3.827-26.577,10.5 c-36.976,34.216-58.766,66.435-71.555,91.496h64.7c15.689,0,29.659,7.48,38.551,19.054c8.402-19.072,17.938-35.094,27.046-46.473 C469.918,213.18,490.104,183.202,460.503,158.94z"></path>
          <path d="M426.034,362.146v-64.33c0-15.773-12.832-28.606-28.605-28.606H78.736c-15.773,0-28.605,12.833-28.605,28.606v64.33 c0,15.773,12.832,28.605,28.605,28.605h318.692C413.202,390.751,426.034,377.919,426.034,362.146z"></path>
          <polygon points="361.503,410.751 366.222,448.111 390.289,448.111 395.008,410.751 	"></polygon>
          <polygon points="81.156,410.751 85.875,448.111 109.943,448.111 114.662,410.751 	"></polygon>
          <path d="M310.398,249.209c13.707-29.733,37.827-66.87,80.301-106.176c5.991-5.544,12.943-9.722,20.426-12.393 C384.207,70.678,316.8,28.053,238.082,28.053c-78.718,0-146.137,42.62-173.056,102.583c7.487,2.671,14.443,6.85,20.438,12.397 c42.474,39.305,66.594,76.441,80.301,106.176H310.398z"></path>
        </svg>
          <?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_KOMFORT_TXT') ?>
      </button>
    </li>
  </ul>
  <div class="multi-popup__popups-wrapper">
    <div class="multi-popup__popup popup-place">
      <h3 class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_LOCATION_TXT') ?></h3>
      <ul class="popup-place__list">
        <li class="popup-place__item-location popup-place__item-location--area popup-place__item-location--active">
          <div class="popup-place__location-header">
            <h4 class="popup-place__location-title"><?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_RAION_TXT') ?></h4>
            <button class="popup-place__location-button button-corner" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_TXT') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_TXT') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN') ?></span></button>
          </div>
          <div class="popup-place__location-wrapper popup-place__location-wrapper--area">
            <ul class="popup-place__areas-list">
                <?php foreach ($data['districts'] as $district): ?>
                  <li class="popup-place__areas-item">
                    <div class="popup-place__areas-header">
                      <h5 class="popup-place__areas-title"><?= $district['title'] ?></h5>
                      <button class="popup-place__areas-button button-corner" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_TXT_MIKRO') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_TXT_MIKRO') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN') ?></span></button>
                    </div>
                    <ul class="popup-place__microareas-list">
                      <li class="popup-place__microareas-item">
                        <label class="label">
                          <input type="checkbox" data-filter="true" data-name="districts" data-value="<?= $district['alias'] ?>" <?= getChecked('districts', $district['alias'], $search_block) ?> >
                            <?= $district['title'] ?>
                        </label>
                      </li>
                        <?php foreach ($district['micro_districts'] as $micro_district): ?>
                          <li class="popup-place__microareas-item">
                            <label class="label">
                              <input type="checkbox" data-filter="true" data-name="micro_districts" data-value="<?= $micro_district['alias'] ?>" <?= getChecked('micro_districts', $micro_district['alias'], $search_block) ?> >
                                <?= $micro_district['title'] ?>
                            </label>
                          </li>
                        <?php endforeach; ?>
                    </ul>
                  </li>
                <?php endforeach; ?>
            </ul>
          </div>
        </li>
        <li class="popup-place__item-location popup-place__item-location--metro">
          <div class="popup-place__location-header popup-place__location-header--metro">
            <h4 class="popup-place__location-title"><?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_TXT_METRO') ?></h4>
            <button class="popup-place__location-button button-corner" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_METRO_OPEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_METRO_OPEN') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN') ?></span></button>
          </div>
          <div class="popup-place__location-wrapper popup-place__location-wrapper--metro">
            <div class="popup-place__line-list-wrapper">
              <ul class="popup-place__line-list">
                  <?php $i = 0;
                  foreach ($data['metro'] as $metro_line => $metro_info): ?>
                    <li class="popup-place__line-item popup-place__line-item<?= $i == 0 ? '--active' : null ?>">
                      <button class="popup-place__line-button popup-place__line-button--<?= $metro_line ?>" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_METRO_OPEN_RED') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_METRO_OPEN_RED') ?>">
                        <span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_METRO_OPEN_RED') ?></span>
                      </button>
                      <p class="popup-place__line-name">(<?= $metro_info['text'] ?>)</p>
                    </li>
                      <?php $i++; endforeach;
                  unset($i); ?>
              </ul>
              <div class="popup-place__stations-wrapper">
                  <?php $i = 0;
                  foreach ($data['metro'] as $metro_line => $metro_info): ?>
                    <ul class="popup-place__stations-list popup-place__stations-list--<?= $metro_line ?> <?= $i == 0 ? 'popup-place__stations-list--active' : null ?>">
                        <?php foreach ($metro_info['values'] as $metro): ?>
                          <li class="popup-place__lstations-item">
                            <label class="label">
                              <input type="checkbox" data-filter="true" data-name="metro" data-value="<?= $metro->alias ?>" <?= getChecked('metro', $metro->alias, $search_block) ?> >
                                <?= $metro->title ?>
                            </label>
                          </li>
                        <?php endforeach; ?>
                    </ul>
                      <?php $i++; endforeach;
                  unset($i); ?>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="multi-popup__popup popup-zona">
      <h3 class="popup-zona__title"><?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_ZONE_OPEN') ?></h3>
      <p class="popup-zona__offers-for buttons-for__for">
        <button class="buttons-for__for__for buttons-for__for__for--women buttons-for__for__for--active" type="button" title="Показать цены для женщин" aria-label="Показать цены для женщин"><b class="women"><span>Для женщин</span></b></button>
        <button class="buttons-for__for__for buttons-for__for__for--men" type="button" title="Показать цены для мужчин" aria-label="Показать цены для мужчин"><b class="men"><span>Для мужчин</span></b></button>
      </p>
      <div class="popup-zona__list-wrapper popup-zona__list-wrapper--women">
          <?php foreach ($data['services'] as $zone => $services): ?>
            <ul class="popup-zona__list popup-zona__list--women">
              <li class="popup-zona__item-organ">
                <div class="popup-zona__organ-header">
                  <h4 class="popup-zona__organ-title"><?= $zone ?></h4>
                  <button class="popup-zona__organ-button" type="button" title="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_ZONE_OPEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SELECT_FILTER_ZONE_OPEN') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN') ?></span></button>
                </div>
                <ul class="popup-zona__organ-list">
                    <?php foreach ($services as $service): ?>
                      <li class="popup-zona__organ-item">
                        <label class="label">
                          <input type="checkbox" data-filter="true" data-name="services" data-value="<?= $service['alias'] ?>" <?= getChecked('services', $service['alias'], $search_block) ?> >
                            <?= $service['title'] ?>
                        </label>
                      </li>
                    <?php endforeach; ?>
                </ul>
              </li>
            </ul>
          <?php endforeach; ?>
      </div>
    </div>
    <div class="multi-popup__popup popup-aparat">
      <h3 class="popup-aparat__title"><?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_TXT') ?></h3>
        <?php foreach ($data['apparatus'] as $type => $apparatus): ?>
          <ul class="popup-aparat__list">
            <li class="popup-aparat__item-kind">
              <div class="popup-aparat__kind-header">
                <h4 class="popup-aparat__kind-title"><?= $type ?></h4>
                <button class="popup-aparat__kind-button button-corner" type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_OPEN') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_APARAT_OPEN') ?>"><span class="visually-hidden"><?= JText::_('COM_LASERCITY_ALL_BUTTON_FILTER_OPEN') ?></span></button>
              </div>
              <ul class="popup-aparat__kind-list">
                  <?php foreach ($apparatus as $apparatus_item): ?>
                    <li class="popup-aparat__kind-item">
                      <label class="label">
                        <input type="checkbox" data-filter="true" data-name="apparatus" data-value="<?= $apparatus_item->alias ?>" <?= getChecked('apparatus', $apparatus_item->alias, $search_block) ?> >
                          <?= $apparatus_item->title ?>
                      </label>
                    </li>
                  <?php endforeach; ?>
              </ul>
            </li>
          </ul>
        <?php endforeach; ?>
    </div>
    <div class="multi-popup__popup popup-comfort">
      <h3 class="popup-comfort__title"><?= JText::_('COM_LASERCITY_ALL_FORM_FILTER_KOMFORT_TXT') ?></h3>
      <ul class="popup-comfort__list">
          <?php foreach ($data['comforts'] as $comfort): ?>
            <li class="popup-comfort__item">
              <label class="label">
                <input class="popup-comfort__item--input" type="checkbox" data-filter="true" data-name="comforts" data-value="<?= $comfort->alias ?>" <?= getChecked('comforts', $comfort->alias, $search_block) ?>>
                  <?= $comfort->title ?>
              </label>
            </li>
          <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="multi-popup__choose">
    <div class="multi-popup__buttons-popup">
      <button class="multi-popup__button-popup multi-popup__button-popup--reset " type="button" title="<?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_FORM_CLOSE_MODAL') ?>"><?= JText::_('COM_LASERCITY_ALL_FORM_CANSEL') ?></button>
      <button class="multi-popup__button-popup multi-popup__button-popup--show button" title="<?= JText::_('COM_LASERCITY_ALL_SHOW_RESULT_ME') ?>" aria-label="<?= JText::_('COM_LASERCITY_ALL_SHOW_RESULT_ME') ?>"><?= JText::_('COM_LASERCITY_ALL_SHOW_ME') ?></button>
    </div>
  </div>
</div>