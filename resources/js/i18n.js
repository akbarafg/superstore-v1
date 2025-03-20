import { createI18n } from 'vue-i18n';

import en from './lang/en.json';
import ps from './lang/ps.json';
import fa from './lang/fa.json';

export const i18n = createI18n({
  legacy: false,
  locale: 'en', 
  fallbackLocale: 'en', 
  messages: { en, ps, fa },
});

import { reactive } from 'vue';

export const i18nState = reactive({
  locale: 'en', // Default language
});

export function setLocale(newLocale) {
  i18nState.locale = newLocale;
}
