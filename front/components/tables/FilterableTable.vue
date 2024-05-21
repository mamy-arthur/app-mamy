<template>
  <div>
    <table-filters
      :filter="filter"
      :modal_show="modal_show"
      :filters_array="filters_array"
      :orders_array="orders_array"
      v-show="!!filterable_props.length"
      :is_header_collapsed="is_header_collapsed"
    >
    </table-filters>

    <el-table
      @header-click="handleModal"
      @sort-change="(event) => handleOrder(event)"
      :header-cell-class-name="renderOrderHeader"
      v-bind="$attrs"
      :data="data"
      @row-click="handleRowClick"
    >
      <slot></slot>
      <!--<slot v-bind:renderFilterHeader="renderFilterHeader" :render-header="renderFilterHeader"></slot>-->
    </el-table>
  </div>
</template>

<script>
import { Table, TableColumn } from 'element-ui'
import TableFilters from '../tables/TableFilters.vue'
import linesClickHandling from '@/mixins/with-table-lines-click-handling.js'

export default {
  name: 'FiltrableTable',
  props: {
    data: { type: Array, default: [] },
    filterable_props: { type: Array, default: [] },
    row_click_action: { type: Function, default: null },
  },
  components: {
    TableFilters,
    [Table.name]: Table,
    [TableColumn.name]: TableColumn,
  },
  mixins: [linesClickHandling],
  data() {
    return {
      filter: false,
      modal_show: false,
      filters_array: [],
      orders_array: [],
    }
  },
  methods: {
    async fetchItems() {
      let filter_query = await this.generateFilterQuery()
      let order_query = await this.generateOrderQuery()
      await this.$parent.applyFilterOrders(filter_query, order_query)
    },
    async handleModal(column, event) {
      const { id, label, property } = column
      let filter_column = this.filterable_props.find((item) => {
        return item.property === property
      })
      console.log('modal', column, event, filter_column)
      if (filter_column) {
        this.filter = { ...filter_column, id, label, property, value: '' }
        await this.showModal()
      }
      return column
    },
    async showModal() {
      this.modal_show = true
    },
    async hideModal() {
      this.modal_show = false
    },
    async removeFilterItem(property) {
      if (!property) return
      let array = this.filters_array.filter(function (ele) {
        return ele.property != property
      })
      this.filters_array = array
    },
    // Order
    async handleOrder(event) {
      //console.log('heeere', event)
      const order = event?.order?.toLowerCase().startsWith('desc')
        ? 'DESC'
        : event?.order?.toLowerCase().startsWith('asc')
        ? 'ASC'
        : false
      if (!order) {
        this.removeOrderItem(event.column.property)
      } else {
        const new_order = {
          id: event.column.id,
          property: event.column.property,
          order: order,
          label: event.column.label,
        }
        const index = this.orders_array
          .map((object) => object.property)
          .indexOf(event.column.property)
        if (index >= 0) {
          this.$set(this.orders_array, index, new_order)
        } else {
          this.orders_array.push(new_order)
        }
      }
    },
    async removeOrderItem(property) {
      if (!property) return
      let array = this.orders_array.filter(function (ele) {
        return ele.property != property
      })
      this.orders_array = array
    },
    // Apply filters & orders
    async applyFiltersOrders() {
      await this.fetchItems()
    },
    async generateFilterQuery() {
      let filter_query = ``
      this.filters_array.map((element, index) => {
        let query_op = ``
        if (index > 0) {
          query_op = `&`
        }
        filter_query += `${query_op}filter_by[]=${element.property},${element.type},${element.value}`
      })
      return filter_query
    },
    async generateOrderQuery() {
      let order_query = ``
      this.orders_array.map((element, index) => {
        order_query += `&order_by[]=${element.property},${element.order}`
      })
      return order_query
    },
    async resetFilters() {
      this.filters_array = []
      this.orders_array = []
      await this.applyFiltersOrders()
    },
    //
    //
    async renderOrderHeader({ row, column, rowIndex, columnIndex }) {
      // update view : el-table support one column sort only :: ONLY IF WE NEED MULTI SORT :: USELESS
      let class_name = ''
      console.log(column.property)
      this.orders_array.map((object) => {
        if (object.property === column.property) {
          if (object.order === 'ASC') {
            class_name = 'ascending'
          }
          if (object.order === 'DESC') {
            class_name = 'descending'
          }
        }
      })
      return class_name
    },
  },
  computed: {
    is_header_collapsed() {
      return this.filters_array.length === 0 && this.orders_array.length === 0
    },
  },
}
</script>
