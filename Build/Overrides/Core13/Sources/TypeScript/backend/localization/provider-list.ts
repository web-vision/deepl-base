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

import { customElement, property } from 'lit/decorators.js';
import { html, LitElement, type TemplateResult } from 'lit';
import { unsafeHTML } from 'lit/directives/unsafe-html.js';
import type { LocalizationProvider } from '@typo3/backend/localization';

@customElement('typo3-localization-wizard-provider-list')
class LocalizationProviders extends LitElement {
  @property({ type: String }) providers: LocalizationProvider[];

  public override render(): TemplateResult {
    return html`${this.providers.map((provider) =>
      html`<div class="row">
        <div class="col-sm-3" style="margin-bottom: 20px;">
          <input class="btn-check t3js-localization-option" type="radio" name="mode" id="${provider.identifier}" value=${provider.identifier}>
          <label class="btn btn-default btn-block-vertical" for="${provider.identifier}" data-action="${provider.identifier}">
            <typo3-backend-icon identifier=${provider.icon} size="large"></typo3-backend-icon>
            ${provider.title}
          </label>
        </div>
        <div class="col-sm-9">
          <p class="t3js-helptext t3js-helptext-copy text-body-secondary">${unsafeHTML(provider.description)}</p>
        </div>
      </div>`
    )}`;
  }

  protected override createRenderRoot(): HTMLElement | ShadowRoot {
    // @todo Switch to Shadow DOM once Bootstrap CSS style can be applied correctly
    return this;
  }
}

declare global {
  interface HTMLElementTagNameMap {
    'typo3-localization-wizard-provider-list': LocalizationProviders;
  }
}
