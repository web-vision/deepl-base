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
var __decorate=function(e,t,i,o){var r,l=arguments.length,a=l<3?t:null===o?o=Object.getOwnPropertyDescriptor(t,i):o;if("object"==typeof Reflect&&"function"==typeof Reflect.decorate)a=Reflect.decorate(e,t,i,o);else for(var n=e.length-1;n>=0;n--)(r=e[n])&&(a=(l<3?r(a):l>3?r(t,i,a):r(t,i))||a);return l>3&&a&&Object.defineProperty(t,i,a),a};import{customElement,property}from"lit/decorators.js";import{html,LitElement}from"lit";import{unsafeHTML}from"lit/directives/unsafe-html.js";let LocalizationProviders=class extends LitElement{render(){return html`${this.providers.map((e=>html`<div class="row">
        <div class="col-sm-3">
          <input class="btn-check t3js-localization-option" type="radio" name="mode" id="${e.identifier}" value=${e.identifier}>
          <label class="btn btn-default btn-block-vertical" for="${e.identifier}" data-action="${e.identifier}">
            <typo3-backend-icon identifier=${e.icon} size="large"></typo3-backend-icon>
            ${e.title}
          </label>
        </div>
        <div class="col-sm-9">
          <p class="t3js-helptext t3js-helptext-copy text-body-secondary">${unsafeHTML(e.description)}</p>
        </div>
      </div>`))}`}createRenderRoot(){return this}};__decorate([property({type:String})],LocalizationProviders.prototype,"providers",void 0),LocalizationProviders=__decorate([customElement("typo3-localization-wizard-provider-list")],LocalizationProviders);