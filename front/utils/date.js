import fr from 'date-fns/locale/fr'
import { parse } from 'date-fns'

export function isValidDate(date) {
  return date instanceof Date && !isNaN(date)
}

export function getLocaleDateFromZ(date, locale = null) {
  let output = new Date(date)

  if (typeof date === 'string') {
    output = parse(
      `${date.substr(0, 19).replace('T', ' ')}Z`,
      'yyyy-MM-dd HH:mm:ssX',
      new Date(),
      { locale: locale || fr }
    )
  }

  console.log(output.getHours())

  return output
}
