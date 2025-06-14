/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

//----------------------------------------------------------------------------------------------------------------------
// BASED on TYPO3 v12.4.0
//----------------------------------------------------------------------------------------------------------------------
import DocumentService from '@typo3/core/document-service';
import $ from 'jquery';
import { AjaxResponse } from '@typo3/core/ajax/ajax-response';
import { SeverityEnum } from './enum/severity';
import AjaxRequest from '@typo3/core/ajax/ajax-request';
import Icons from './icons';
import Wizard from './wizard';
import '@typo3/backend/element/icon-element';
import { MarkupIdentifiers, Sizes } from './enum/icon-types';
import Modal, { type ModalElement } from './modal';

export type LocalizationProvider = {
  identifier: string;
  icon: string;
  title: string;
  description: string;
};

type LanguageRecord = {
  uid: number;
  title: string;
  flagIcon: string;
};

type SummaryColumns = {
  columns: { [key: number]: string };
  columnList: Array<number>;
};

type SummaryColPosRecord = {
  uid: number;
  title: string;
  icon: string;
};

type SummaryRecord = {
  columns: SummaryColumns;
  records: Array<Array<SummaryColPosRecord>>;
};

class Localization {
  private triggerButton: string = '.t3js-localize';
  private localizationMode: string = null;
  private sourceLanguage: number = null;
  private records: Array<any> = [];

  constructor() {
    DocumentService.ready().then((): void => {
      this.initialize();
    });
  }

  private initialize(): void {
    const me = this;
    $(me.triggerButton).removeClass('disabled');
    $(document).on('click', me.triggerButton, async (e: JQueryEventObject): Promise<void> => {
      e.preventDefault();

      const $triggerButton = $(e.currentTarget);
      const pageId = parseInt($triggerButton.data('pageId'), 10);
      const languageId = parseInt($triggerButton.data('languageId'), 10);
      const availableLocalizationModes: LocalizationProvider[] = await (await this.getLocalizationProviders(pageId, languageId)).resolve();

      if (availableLocalizationModes.length === 0) {
        Modal.confirm(
          TYPO3.lang['window.localization.mixed_mode.title'],
          TYPO3.lang['window.localization.mixed_mode.message'],
          SeverityEnum.warning,
          [
            {
              text: TYPO3?.lang?.['button.ok'] || 'OK',
              btnClass: 'btn-warning',
              name: 'ok',
              trigger: (e: Event, modal: ModalElement): void => modal.hideModal()
            }
          ]
        );
        return;
      }

      const actions: Array<string> = await Promise.all(availableLocalizationModes.map(async (provider: LocalizationProvider): Promise<string> => {
        return this.createProviderMarkup(provider);
      }));
      const slideStep1: string = '<div data-bs-toggle="buttons">' + actions.join('') + '</div>';
      Wizard.addSlide(
        'localize-choose-action',
        TYPO3.lang['localize.wizard.header_page']
          .replace('{0}', $triggerButton.data('page'))
          .replace('{1}', $triggerButton.data('languageName')),
        slideStep1,
        SeverityEnum.info,
        (): void => {
          if (availableLocalizationModes.length === 1) {
            // In case only one mode is available, select the mode and continue
            this.localizationMode = availableLocalizationModes[0].identifier;
            Wizard.unlockNextStep().trigger('click');
          }
        }
      );
      Wizard.addSlide(
        'localize-choose-language',
        TYPO3.lang['localize.view.chooseLanguage'],
        '',
        SeverityEnum.info,
        ($slide: JQuery): void => {
          Icons.getIcon('spinner-circle-dark', Icons.sizes.large).then((markup: string): void => {
            $slide.html('<div class="text-center">' + markup + '</div>');

            this.loadAvailableLanguages(
              parseInt($triggerButton.data('pageId'), 10),
              parseInt($triggerButton.data('languageId'), 10),
            ).then(async (response: AjaxResponse): Promise<void> => {
              const result: Array<LanguageRecord> = await response.resolve();
              if (result.length === 1) {
                // We only have one result, auto select the record and continue
                this.sourceLanguage = result[0].uid;
                Wizard.unlockNextStep().trigger('click');
                return;
              }

              Wizard.getComponent().on('click', '.t3js-language-option', (optionEvt: JQueryEventObject): void => {
                const $me = $(optionEvt.currentTarget);
                const $radio = $me.prev();

                this.sourceLanguage = $radio.val();
                Wizard.unlockNextStep();
              });

              const $languageButtons = $('<div />', { class: 'row' });

              for (const languageObject of result) {
                const id: string = 'language' + languageObject.uid;
                const $input: JQuery = $('<input />', {
                  type: 'radio',
                  name: 'language',
                  id: id,
                  value: languageObject.uid,
                  style: 'display: none;',
                  class: 'btn-check'
                });
                const $label: JQuery = $('<label />', { class: 'btn btn-default d-block t3js-language-option option', 'for': id })
                  .text(' ' + languageObject.title)
                  .prepend(languageObject.flagIcon);

                $languageButtons.append(
                  $('<div />', { class: 'col-sm-4' })
                    .append($input)
                    .append($label),
                );
              }
              $slide.empty().append($languageButtons);
            });
          });
        },
      );
      Wizard.addSlide(
        'localize-summary',
        TYPO3.lang['localize.view.summary'],
        '',
        SeverityEnum.info, ($slide: JQuery): void => {
          Icons.getIcon('spinner-circle-dark', Icons.sizes.large).then((markup: string): void => {
            $slide.html('<div class="text-center">' + markup + '</div>');
          });
          this.getSummary(
            parseInt($triggerButton.data('pageId'), 10),
            parseInt($triggerButton.data('languageId'), 10),
          ).then(async (response: AjaxResponse): Promise<void> => {
            const result: SummaryRecord = await response.resolve();
            $slide.empty();
            this.records = [];

            const columns = result.columns.columns;
            const columnList = result.columns.columnList;

            columnList.forEach((colPos: number): void => {
              if (typeof result.records[colPos] === 'undefined') {
                return;
              }

              const column = columns[colPos];
              const $row = $('<div />', { class: 'row' });

              result.records[colPos].forEach((record: SummaryColPosRecord): void => {
                const label = ' (' + record.uid + ') ' + record.title;
                this.records.push(record.uid);

                $row.append(
                  $('<div />', { 'class': 'col-sm-6' }).append(
                    $('<div />', { 'class': 'input-group' }).append(
                      $('<span />', { 'class': 'input-group-addon' }).append(
                        $('<input />', {
                          type: 'checkbox',
                          'class': 't3js-localization-toggle-record',
                          id: 'record-uid-' + record.uid,
                          checked: 'checked',
                          'data-uid': record.uid,
                          'aria-label': label,
                        }),
                      ),
                      $('<label />', {
                        'class': 'form-control',
                        for: 'record-uid-' + record.uid,
                      }).text(label).prepend(record.icon),
                    ),
                  ),
                );
              });

              $slide.append(
                $('<fieldset />', {
                  'class': 'localization-fieldset',
                }).append(
                  $('<label />').text(column).prepend(
                    $('<input />', {
                      'class': 't3js-localization-toggle-column',
                      type: 'checkbox',
                      checked: 'checked',
                    }),
                  ),
                  $row,
                ),
              );
            });

            Wizard.unlockNextStep();

            Wizard.getComponent().on('change', '.t3js-localization-toggle-record', (cmpEvt: JQueryEventObject): void => {
              const $me = $(cmpEvt.currentTarget);
              const uid = $me.data('uid');
              const $parent = $me.closest('fieldset');
              const $columnCheckbox = $parent.find('.t3js-localization-toggle-column');

              if ($me.is(':checked')) {
                this.records.push(uid);
              } else {
                const index = this.records.indexOf(uid);
                if (index > -1) {
                  this.records.splice(index, 1);
                }
              }

              const $allChildren = $parent.find('.t3js-localization-toggle-record');
              const $checkedChildren = $parent.find('.t3js-localization-toggle-record:checked');

              $columnCheckbox.prop('checked', $checkedChildren.length > 0);
              $columnCheckbox.prop('indeterminate', $checkedChildren.length > 0 && $checkedChildren.length < $allChildren.length);

              if (this.records.length > 0) {
                Wizard.unlockNextStep();
              } else {
                Wizard.lockNextStep();
              }
            }).on('change', '.t3js-localization-toggle-column', (toggleEvt: JQueryEventObject): void => {
              const $me = $(toggleEvt.currentTarget);
              const $children = $me.closest('fieldset').find('.t3js-localization-toggle-record');

              $children.prop('checked', $me.is(':checked'));
              $children.trigger('change');
            });
          });
        },
      );

      Wizard.addFinalProcessingSlide((): void => {
        this.localizeRecords(
          parseInt($triggerButton.data('pageId'), 10),
          parseInt($triggerButton.data('languageId'), 10),
          this.records,
        ).then((): void => {
          Wizard.dismiss();
          document.location.reload();
        });
      }).then((): void => {
        Wizard.show();

        Wizard.getComponent().on('click', '.t3js-localization-option', (optionEvt: JQueryEventObject): void => {
          const $me = $(optionEvt.currentTarget);
          const $radio = $me.find('input[type="radio"]');

          if ($me.data('helptext')) {
            const $container = $(optionEvt.delegateTarget);
            $container.find('.t3js-localization-option').removeClass('active');
            $container.find('.t3js-helptext').addClass('text-body-secondary');
            $me.addClass('active');
            $container.find($me.data('helptext')).removeClass('text-body-secondary');
          }
          this.localizationMode = $radio.val();
          Wizard.unlockNextStep();
        });
      });
    });
  }

  /**
   * Load available languages from page
   *
   * @param {number} pageId
   * @param {number} languageId
   * @returns {Promise<AjaxResponse>}
   */
  private loadAvailableLanguages(pageId: number, languageId: number): Promise<AjaxResponse> {
    return new AjaxRequest(TYPO3.settings.ajaxUrls.page_languages).withQueryArguments({
      pageId: pageId,
      languageId: languageId,
    }).get();
  }

  /**
   * Get summary for record processing
   *
   * @param {number} pageId
   * @param {number} languageId
   * @returns {Promise<AjaxResponse>}
   */
  private getSummary(pageId: number, languageId: number): Promise<AjaxResponse> {
    return new AjaxRequest(TYPO3.settings.ajaxUrls.records_localize_summary).withQueryArguments({
      pageId: pageId,
      destLanguageId: languageId,
      languageId: this.sourceLanguage,
      localizationMode: this.localizationMode,
    }).get();
  }

  /**
   * Localize records
   *
   * @param {number} pageId
   * @param {number} languageId
   * @param {Array<number>} uidList
   * @returns {Promise<AjaxResponse>}
   */
  private localizeRecords(pageId: number, languageId: number, uidList: Array<number>): Promise<AjaxResponse> {
    return new AjaxRequest(TYPO3.settings.ajaxUrls.records_localize).withQueryArguments({
      pageId: pageId,
      srcLanguageId: this.sourceLanguage,
      destLanguageId: languageId,
      action: this.localizationMode,
      uidList: uidList,
    }).get();
  }

  private getLocalizationProviders(pageId: number, languageId: number): Promise<AjaxResponse> {
    return new AjaxRequest(TYPO3.settings.ajaxUrls.records_localize_providers).withQueryArguments({
      uid: pageId,
      languageId: languageId,
    }).get();
  }

  private async createProviderMarkup(provider: LocalizationProvider): Promise<string>
  {
    const iconMarkup: string = await Icons.getIcon(provider.icon, Icons.sizes.large, null, null, MarkupIdentifiers.inline);
    return Promise.resolve(
      '<div class="row">'
      + '<div class="col-sm-3">'
      + '<label class="btn btn-default d-block t3js-localization-option" data-helptext=".t3js-helptext-translate">'
      + iconMarkup
      + '<input type="radio" name="mode" id="' + provider.identifier + '" value="' + provider.identifier + '" style="display: none">'
      + '<br>' + provider.title + '</label>'
      + '</div>'
      + '<div class="col-sm-9">'
      + '<p class="t3js-helptext t3js-helptext-translate text-body-secondary">' + provider.description + '</p>'
      + '</div>'
      + '</div>'
    );
  }
}

export default new Localization();
