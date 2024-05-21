<template>
  <div>
    <el-table
      :columns="columns" 
      :hoverRowIndex="hoverRowIndex"
      :rows="rows">
    </el-table>
    <base-pagination
      align="end"
      v-model="page"
      v-on:input="updatePagination"
      :total="total_items"
      :per-page="items_per_page"
    ></base-pagination>
  </div>
</template>

<script>
import {
  Dropdown,
  DropdownItem,
  DropdownMenu,
  Table,
  TableColumn,
} from 'element-ui'
import withPaginatedList from '@/mixins/with-paginated-list.mixin'
import translator from '@/mixins/translator.mixin'

export default {
  name: 'TableFilter',
  components: {
    [Table.name]: Table,
    [TableColumn.name]: TableColumn,
    [Dropdown.name]: Dropdown,
    [DropdownItem.name]: DropdownItem,
    [DropdownMenu.name]: DropdownMenu,
  },
  mixins: [withPaginatedList, translator],
  data() {
    return {
      rows: [],
      columns: [],
    }
  },
  methods: {
    
  },
}
</script>
