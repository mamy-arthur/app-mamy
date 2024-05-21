<template>
  <el-table
    class="table-responsive table-flush"
    header-row-class-name="thead-light"
    :data="roles"
    @row-click="rowClick"
  >
    <el-table-column class-name="actions-column">
      <template v-slot="{ row }">
        <base-button type="secondary">
          <nuxt-link :to="`/users/role/${row.id}`">
            <i class="fa fa-edit"></i>
          </nuxt-link>
        </base-button>
      </template>
    </el-table-column>
    <el-table-column label="Nom" prop="name" min-width="120px" sortable>
    </el-table-column>
    <el-table-column label="Code" prop="code" min-width="80px" sortable>
    </el-table-column>
  </el-table>
</template>

<script>
import {
  Dropdown,
  DropdownItem,
  DropdownMenu,
  Table,
  TableColumn,
} from 'element-ui'

export default {
  name: 'UsersRolesListTable',
  components: {
    [Table.name]: Table,
    [TableColumn.name]: TableColumn,
    [Dropdown.name]: Dropdown,
    [DropdownItem.name]: DropdownItem,
    [DropdownMenu.name]: DropdownMenu,
  },
  async fetch() {
    /** @var array */
    this.roles = await this.$axios.$get(`/auth-api/roles`)
  },
  data() {
    return {
      roles: [],
    }
  },
    methods: {
    rowClick(row, column, event) {
      if ( event.pointerType == 'touch' && (!column.hasOwnProperty('className') || column.className != 'actions-column' ) ) {
        this.$router.push(`/users/role/${row.id}`)
      }
    }
  },
}
</script>
