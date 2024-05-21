<template>
  <div class="period-filter">
    <button-radio-group
      :options="toggles"
      button-classes="btn-secondary filter-toggle"
      v-model="active_toggle"
    >
    </button-radio-group>
    <div v-show="is_period_selection_allowed" class="period-form">
      <span class="mr-2 align-self-center">{{ LABELS.FROM_DATE }}</span>
      <base-input
        :required="false"
        class="mr-2"
        addon-left-icon="ni ni-calendar-grid-58"
      >
        <flat-picker
          slot-scope="{ focus, blur }"
          @on-open="focus"
          @on-close="blur"
          :config="datetimeConfig"
          class="form-control datepicker"
          v-model="from_date"
        >
        </flat-picker>
      </base-input>
      <span class="mr-2 align-self-center">{{ LABELS.TO_DATE }}</span>
      <base-input
        :required="false"
        class="mr-2"
        addon-left-icon="ni ni-calendar-grid-58"
      >
        <flat-picker
          slot-scope="{ focus, blur }"
          @on-open="focus"
          @on-close="blur"
          :config="datetimeConfig"
          class="form-control datepicker"
          v-model="to_date"
        >
        </flat-picker>
      </base-input>
      <!--      todo: handle the rendering and handling of the buttons-->
      <!--      <base-button-->
      <!--        class="btn btn-md btn-primary mr-2"-->
      <!--        native-type="search"-->
      <!--        type="primary"-->
      <!--        v-on:click="applyFilters"-->
      <!--        ><i class="fas fa-search"></i-->
      <!--      ></base-button>-->
      <!--      <base-button-->
      <!--        class="btn btn-md btn-secondary btn-outline-secondary action-warning"-->
      <!--        native-type="reset"-->
      <!--        type="primary"-->
      <!--        v-on:click="clearFilters"-->
      <!--        v-show="are_filters_dirty"-->
      <!--        ><i class="fas fa-times"></i-->
      <!--      ></base-button>-->
    </div>
  </div>
</template>
<script>
import ButtonRadioGroup from '@/components/argon-core/ButtonRadioGroup'
import { endOfDay, endOfToday, startOfToday } from 'date-fns'
import { TOGGLE_VALUE } from '@/components/filters/PeriodFilter/constants'
import { LABELS } from '@/components/filters/PeriodFilter/labels'
import { French } from 'flatpickr/dist/l10n/fr.js'
import flatPicker from 'vue-flatpickr-component'
import withDateData from '@/mixins/with-date-data.mixin'

export default {
  components: { ButtonRadioGroup, flatPicker },
  mixins: [withDateData],
  props: {
    filter: {
      type: [String, Object],
      description: 'Current filter data',
    },
  },
  model: {
    prop: 'filter',
    event: 'change',
  },
  mounted() {
    if (typeof this.filter === 'string') {
      this.setModel(this.filter)
    }
  },
  data() {
    return {
      toggles: [
        { value: TOGGLE_VALUE.TODAY, label: LABELS.TODAY },
        // { value: 'this_week', label: 'Cette semaine' }, todo
        // { value: 'this_month', label: 'Ce mois' }, todo
        { value: TOGGLE_VALUE.A_PERIOD, label: LABELS.THE_PERIOD },
      ],
      active_toggle: 'today',
      datetimeConfig: {
        enableTime: false,
        allowInput: true,
        locale: French,
      },
      from_date: startOfToday(),
      to_date: endOfToday(),
      LABELS,
    }
  },
  methods: {
    getTimeInterval(toggle) {
      let output

      switch (toggle) {
        case TOGGLE_VALUE.TODAY:
          output = [startOfToday(), endOfToday()]
          break
        case TOGGLE_VALUE.A_PERIOD:
          const from_date = this.parseDatetime(this.from_date, 'yyyy-MM-dd')
          let to_date = this.parseDatetime(this.to_date, 'yyyy-MM-dd')
          if (to_date) {
            to_date = endOfDay(to_date)
          }

          output = [from_date || null, to_date || null]
          break
      }

      return output
    },
    setModel(toggle) {
      const model = {
        selected: toggle,
        startDate: null,
        endDate: null,
      }
      const period = this.getTimeInterval(toggle)

      if (period) {
        model.startDate = period[0]
        model.endDate = period[1]
      }

      this.model = model
    },
  },
  computed: {
    model: {
      get() {
        return this.filter
      },
      set(val) {
        this.$emit('change', val)
      },
    },
    is_period_selection_allowed() {
      return this.active_toggle === TOGGLE_VALUE.A_PERIOD
    },
  },
  watch: {
    active_toggle(new_value, old_value) {
      this.setModel(new_value)
    },
    from_date() {
      this.setModel(this.active_toggle)
    },
    to_date() {
      this.setModel(this.active_toggle)
    },
  },
}
</script>

<style scoped lang="scss">
/deep/ .filter-toggle {
  border-radius: 1.25rem;
}

.period-filter {
  display: flex;
}

.period-form {
  display: inline-flex;
  flex-wrap: wrap;
  flex-direction: row;
  margin: 0;
  padding: 0 1rem;
  font-size: 0.9rem;

  /deep/ .form-group {
    margin: 0;

    input {
      min-width: 150pt;
    }
  }
}
</style>
