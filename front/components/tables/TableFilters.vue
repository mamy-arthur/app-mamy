<template>
  <div>
    <collapse :active-index="is_header_collapsed ? -1 : 0">
      <collapse-item name="filters-n-orders">
        <h3 slot="title" class="mb-0">{{ `Filtres & Tris` }}</h3>
        <div class="filter-content">
          <div class="filter-section">
            <label>Filtres : </label>
            <div class="filter-items" v-if="filters_array.length">
              <div
                class="filter-item"
                v-for="item in filters_array"
                :key="item.id"
              >
                <label>{{ item.label }}:</label>
                <span>{{ item.value }}</span>
                <!--<span v-for="value, index in item.values" :key="index">{{ value }}</span>-->
                <i
                  class="fa fa-times"
                  @click="removeFilterItem(item.property)"
                ></i>
              </div>
            </div>
            <div class="no-filters" v-if="!filters_array.length">
              <p>Aucun filtre n'a été sélectionné</p>
            </div>
          </div>
          <div class="filter-section">
            <label>Tris : </label>
            <div class="filter-items" v-if="orders_array.length">
              <div
                class="filter-item"
                v-for="item in orders_array"
                :key="item.id"
              >
                <label>{{ item.label }}:</label>
                <span>{{ item.order }}</span>
                <i
                  class="fa fa-times"
                  @click="removeOrderItem(item.property)"
                ></i>
              </div>
            </div>
            <div class="no-filters" v-if="!orders_array.length">
              <p>Aucun tri n'a été sélectionné</p>
            </div>
          </div>
          <div class="row filter-footer actions-zone col-md-6 offset-md-6">
            <div class="col-sm">
              <base-button
                type="secondary"
                native-type="reset"
                @click="resetFilters"
                class="col"
                >Réinitialiser</base-button
              >
            </div>
            <div class="col-sm">
              <base-button
                type="primary"
                native-type="button"
                @click="applyFiltersOrders"
                class="col"
                >Appliquer</base-button
              >
            </div>
          </div>
        </div>
      </collapse-item>
    </collapse>

    <modal :show.sync="modal_show">
      <filter-modal
        :filter="filter"
        v-on:filter:apply="applyFilter"
        v-on:filter:cancel="cancelFilter"
      ></filter-modal>
    </modal>
  </div>
</template>

<script>
import { Collapse, CollapseItem } from '@/components/argon-core'
import FilterModal from '@/components/tables/TableFilters/FilterModal'

export default {
  name: 'TableFilters',
  props: {
    filters_array: { type: Array, default: [] },
    orders_array: { type: Array, default: [] },
    filter: { type: [Object, Boolean], default: false },
    modal_show: { type: Boolean, default: false },
    is_header_collapsed: { type: Boolean, default: true },
  },
  components: {
    FilterModal,
    Collapse,
    CollapseItem,
  },
  async fetch() {},
  data() {
    return {}
  },
  methods: {
    async applyFilter(event) {
      if (event.value && event.property) {
        await this.updateFilters(event)
      }
      await this.$parent.hideModal()
    },
    async updateFilters(event) {
      const index = this.filters_array
        .map((object) => object.property)
        .indexOf(event.property)
      if (index >= 0) {
        let old_filter = this.filters_array[index]
        old_filter.value = event.value
        old_filter.values.push(event.value)
        this.$set(this.filters_array, index, old_filter)
      } else {
        let new_filter = {
          id: event.id,
          label: event.label,
          property: event.property,
          type: event.type,
          value: event.value,
          values: [event.value],
        }
        this.filters_array.push(new_filter)
      }
    },
    async cancelFilter() {
      await this.$parent.hideModal()
    },
    async resetFilters() {
      await this.$parent.resetFilters()
    },
    async applyFiltersOrders() {
      await this.$parent.applyFiltersOrders()
    },
    async removeFilterItem(property) {
      await this.$parent.removeFilterItem(property)
    },
    async removeOrderItem(property) {
      await this.$parent.removeOrderItem(property)
    },
  },
}
</script>

<style lang="scss" scoped>
/deep/.card-header {
  padding: 0.5rem 1.5rem;
}
/deep/.card-body {
  padding: 0 1.5rem;
}
.filter-content {
  padding: 0px;
  box-shadow: 0 4px 6px rgb(50, 50, 93) 0 1px 3px rgb(0, 0, 0);
  background-color: #fff;
  color: #575756;
  border: 1px solid #ebeef5;
  margin-bottom: 20px;
  p {
    margin: 0;
    padding: 0;
  }
  .filter-section {
    display: flex;
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #f1f1f1;
    label {
      font-size: 14px;
      text-decoration: none;
      margin-right: 15px;
    }
    .filter-items {
      .filter-item {
        background: rgba(50, 50, 93, 0.1098);
        padding: 4px 11px;
        border-radius: 4px;
        display: inline-flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        align-content: center;
        margin: 0 11px 0 0;
        label {
          font-size: 12px;
          font-weight: 600;
          margin: 0 5px 0 0;
          text-decoration: none;
        }
        span {
          font-size: 12px;
          margin: 0 5px;
        }
        i {
          margin: 2px 0 0 5px;
          display: flex;
          vertical-align: middle;
          cursor: pointer;
        }
      }
    }
  }
  .filter-footer {
    padding: 0.5rem 0;
  }
}
</style>
