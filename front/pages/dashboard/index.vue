<template>
  <div class="container-fluid mt-30">
    <div class="row">
      <div class="col-sm-9" v-show="!loading">
      </div>
      <div class="col-sm-3" v-show="!loading">
      </div>
    </div>
  </div>
</template>

<script>
import { getRandomString } from '@/utils/string'
import {
  endOfDay,
  endOfMonth,
  endOfToday,
  endOfWeek,
  startOfDay,
  startOfMonth,
  startOfToday,
  startOfWeek,
} from 'date-fns'
import withDateData from '@/mixins/with-date-data.mixin'
import fr from 'date-fns/locale/fr'
import withPaginatedList from '@/mixins/with-paginated-list.mixin'

export default {
  mixins: [withDateData, withPaginatedList],
  head() {
    return {
      title: 'Dashboard',
    }
  },
  data() {
    return {
      filter: {
        type: 'day',
        start_date: 'day',
        end_date: false,
        selected_dates: new Date(), //[new Date(), new Date()]
      },
      loading: true,
      flights_folders: [],
      parking_spots: [],
      flights_overview_key: getRandomString(),
      parking_overview_key: getRandomString(),
      time_interval: {
        from_date: startOfToday(),
        to_date: endOfToday(),
      },
      total_folders: 0
    }
  },
  created() {
    this.fetchData()
  },
  mounted() {
    if ( this.$store.state.permissions.is_redirection ) {
      this.$notify({
        type: 'warning',
        verticalAlign: 'bottom',
        horizontalAlign: 'right',
        message: "Vous n'avez pas le droit d'accéder à une ou plusieurs des fonctionnalités demandées.",
        timeout: 10000
      })
      this.$store.commit('permissions/set_is_redirection', false)
    }
  },
  methods: {
    async updatePagination(page) {
      // Call applyFilters to refresh the table data
      this.page = page
      await this.fetchFlightsFolders()
    },
    updateTimeInterval({ span, reference_date }) {
      reference_date = this.isValidDate(new Date(reference_date))
        ? new Date(reference_date)
        : new Date()

      switch (span) {
        case 'day':
        case 'days': // todo: this case (day's') will need update later
          this.time_interval = {
            ...this.time_interval,
            from_date: startOfDay(reference_date),
            to_date: endOfDay(reference_date),
          }
          break
        case 'week':
          this.time_interval = {
            ...this.time_interval,
            from_date: startOfWeek(reference_date, { locale: fr }),
            to_date: endOfWeek(reference_date, { locale: fr }),
          }
          break
        case 'month':
          this.time_interval = {
            ...this.time_interval,
            from_date: startOfMonth(reference_date),
            to_date: endOfMonth(reference_date),
          }
          break
      }
    },
    async fetchData() {
      this.$nextTick(async () => {
        this.$nuxt.$loading.start()

        this.loading = true

        this.$nuxt.$loading.finish()
      })
    },
  },
  watch: {
    async time_interval(value) {
      await this.fetchData()
    },
  },
}
</script>
<style>
.mt-20 {
  margin-top: 30px;
}
</style>
