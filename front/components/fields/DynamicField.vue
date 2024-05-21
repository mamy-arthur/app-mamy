<!-- todo: move to another folder-->
<template>
  <div
    v-if="
      field.field_type === 'select' || field.field_type === 'select-multiple'
    "
  >
    <base-input
      :label="field.label"
      :name="field.label"
      :rules="{ required: field.is_required }"
      :required="field.is_required"
    >
      <el-select
        filterable
        :multiple="field.field_type === 'select-multiple'"
        placeholder="-- Choisissez une valeur --"
        :value="value"
        @input="emitValue"
        :remote-method="queryChoices"
        :remote="with_remote_choices"
        :clearable="!field.is_required"
        default-first-option
      >
        <el-option
          v-for="(item, index) in with_remote_choices
            ? choices_filtered
            : choices"
          :value="
            field.choices.value_property
              ? item[field.choices.value_property]
              : index
          "
          :key="
            field.choices.value_property
              ? item[field.choices.value_property]
              : index
          "
          :label="
            typeof item !== 'object'
              ? item + ` (${index})`
              : item.label || item.name || item[field.choices.value_property]
          "
        ></el-option>
      </el-select>
    </base-input>
  </div>
  <div v-else-if="field.field_type === 'datetime'">
    <base-input
      :label="field.label"
      :name="field.label"
      :required="field.is_required"
      addon-left-icon="ni ni-calendar-grid-58"
    >
      <flat-picker
        slot-scope="{ focus, blur }"
        @on-open="focus"
        @on-close="blur"
        :config="datetimeConfig"
        class="form-control datepicker"
        :value="value"
        @input="emitValue"
      >
      </flat-picker>
    </base-input>
  </div>
  <div v-else>
    <base-input
      :label="field.label"
      :name="field.label"
      :required="field.is_required"
      :type="field.field_type"
      :value="value"
      @input="emitValue"
    ></base-input>
  </div>
</template>

<script>
import { Option, Select } from 'element-ui'
import { French } from 'flatpickr/dist/l10n/fr.js'
import flatPicker from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

export default {
  name: 'DynamicField',
  components: { [Select.name]: Select, [Option.name]: Option, flatPicker },
  props: {
    field: Object,
    value: {
      required: true,
    },
    option_data: [],
  },
  async fetch() {
    this.choices = await this.getChoices(this.field)

    if (this.with_remote_choices) {
      const choices = this.choices.slice(0, 20)
      if (this.value) {
        const selected = this.choices.find(
          (choice) => choice[this.field.choices.value_property] === this.value
        )
        if (
          !choices.find(
            (choice) => choice[this.field.choices.value_property] === this.value
          )
        ) {
          choices.unshift(selected)
        }
      }

      this.choices_filtered = choices
    }
  },
  data() {
    return {
      choices_filtered: [],
      choices: [],
      datetimeConfig: {
        enableTime: true,
        allowInput: true,
        locale: French,
      },
    }
  },
  methods: {
    emitValue(value) {
      this.$emit('input', value)
      this.$emit(`${this.field.name}:changed`, value)
    },
    async getChoices() {
      let values = []

      if (typeof this.field.choices?.values === 'string') {
        if (this.option_data?.length > 0) {
          values = this.option_data
        } else {
          values = await this.$axios.$get(this.field.choices.values)
        }
      } else {
        values = this.field.choices
      }

      return values
    },
    queryChoices(query) {
      if (query && this.choices && !this.field.loading) {
        this.field.loading = true
        this.choices_filtered = this.choices.filter((item, index) => {
          const item2 =
            item[
              this.field.choices.value_property
                ? this.field.choices.value_property
                : index
            ]
          if (typeof item2 == 'number') {
            return item.label?.toLowerCase().includes(query.toLowerCase())
          } else {
            const item3 = item2?.toLowerCase()
            return (
              item3.includes(query.toLowerCase()) ||
              item.label?.toLowerCase().includes(query.toLowerCase())
            )
          }
        })

        this.field.loading = false
      }
    },
  },
  computed: {
    with_remote_choices() {
      return typeof this.field.choices?.values === 'string'
    },
  },
}
</script>

<style scoped>
/deep/ .form-group.is-invalid input {
  border-color: #fb6340;
}
</style>
