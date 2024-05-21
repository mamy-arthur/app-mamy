// We initialize calendar components here since they don't have SSR support.
// We will render them on the client only by making this plugin ssr:false
import Vue from 'vue'
import CalendarWidget from '@/components/widgets/CalendarWidget'
Vue.component(CalendarWidget.name, CalendarWidget)
