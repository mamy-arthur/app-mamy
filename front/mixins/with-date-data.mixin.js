import { add, format, parse, sub } from 'date-fns'
import fr from 'date-fns/locale/fr'

export default {
  data() {
    return {
      DATETIME_FORMAT: 'yyyy-MM-dd HH:mm',
      DATETIME_FORMAT_ALT: 'dd/MM/yyyy HH:mm',
    }
  },
  methods: {
    isValidDate(date) {
      return date instanceof Date && !isNaN(date)
    },
    /**
     * @see dateFns/format
     *
     * @param {Date | number} date
     * @param {string} date_format
     * @param {{locale?: Locale; weekStartsOn?: 0 | 1 | 2 | 3 | 4 | 5 | 6; firstWeekContainsDate?: number; useAdditionalWeekYearTokens?: boolean; useAdditionalDayOfYearTokens?: boolean}} options
     * @return {string}
     */
    formatDatetime(date, date_format, options = undefined) {
      return date && !isNaN(date)
        ? format(date, date_format || this.DATETIME_FORMAT_ALT, options)
        : ''
    },
    /**
     * @see dateFns/add
     *
     * @param {Date | number} date
     * @param {Duration} duration
     * @return Date
     */
    addDuration(date, duration) {
      return add(date, duration)
    },
    /**
     * @see dateFns/sub
     *
     * @param {Date | number} date
     * @param {Duration} duration
     * @return Date
     */
    substractDuration(date, duration) {
      return sub(date, duration)
    },
    /**
     * @see dateFns/parse
     *
     * @param {string} date
     * @param {string} format
     * @param {Date | number} referenceDate
     * @param {{locale?: Locale; weekStartsOn?: 0 | 1 | 2 | 3 | 4 | 5 | 6; firstWeekContainsDate?: 1 | 2 | 3 | 4 | 5 | 6 | 7; useAdditionalWeekYearTokens?: boolean; useAdditionalDayOfYearTokens?: boolean}} options
     */
    parseDatetime(date, format, referenceDate, options = {}) {
      let output

      if (date) {
        try {
          output = new Date(date)
        } catch (e) {
          output = parse(
            date,
            format || this.DATETIME_FORMAT,
            referenceDate,
            options
          )
        }
      }

      return output
    },

    getDayName(date) {
      return this.formatDatetime(new Date(date), 'EEEE', { locale: fr })
    },
  },
}
