<template>
  <div>
    <filterable-table
      :data="users"
      :filterable_props="filterable_props"
      :row_click_action="row_click_action"
    >
      <el-table-column
        class-name="actions-column"
        v-if="can('user', 'entity', 'update')"
      >
        <template v-slot="{ row }">
          <base-button type="secondary">
            <nuxt-link :to="`/users/user/${row.id}`">
              <i class="fa fa-edit"></i>
            </nuxt-link>
          </base-button>
        </template>
      </el-table-column>
      <el-table-column label="Nom" prop="last_name" min-width="100px" sortable>
      </el-table-column>
      <el-table-column
        label="Prénom"
        prop="first_name"
        min-width="100px"
        sortable
      >
      </el-table-column>
      <el-table-column label="Adresse" min-width="150px" prop="address">
      </el-table-column>
      <el-table-column label="Service" min-width="130px" prop="name" sortable>
      </el-table-column>
      <el-table-column label="Email" min-width="150px" prop="email" sortable>
      </el-table-column>
      <el-table-column
        label="Matricule"
        min-width="120px"
        prop="registration_number"
        sortable
      >
      </el-table-column>
      <el-table-column
        label="Actif"
        prop="is_active"
        sortable
        :formatter="translateBoolean"
        class-name="actions-column"
      >
        <template v-slot="{ row }" v-if="can('user', 'entity', 'update')">
          <span @click="clickActive(row)">
            <base-switch v-model="row.is_active"></base-switch>
          </span>
        </template>
      </el-table-column>
    </filterable-table>

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
import { TableColumn } from 'element-ui'
import translator from '@/mixins/translator.mixin'
import withPaginatedList from '@/mixins/with-paginated-list.mixin'
import FilterableTable from '../tables/FilterableTable.vue'
import FilterableTableColumn from '../tables/FilterableTableColumn.vue'

export default {
  name: 'UsersListTable',
  components: {
    FilterableTable,
    FilterableTableColumn,
    [TableColumn.name]: TableColumn,
  },
  mixins: [withPaginatedList, translator],
  async fetch() {
    await this.fetchItems()
  },
  data() {
    return {
      page: 1,
      filterable_props: [
        // todo: filtering doesn't work on the listing... check why
        // { property: 'first_name', type: 'textIncludesSome', multiple: false },
        // { property: 'last_name', type: 'textIncludesSome', multiple: false },
        // { property: 'address', type: 'textIncludesSome', multiple: false },
      ],
      filter_query: false,
      order_query: false,
      users: [],
    }
  },
  methods: {
    async applyFilterOrders(filter_query = false, order_query = false) {
      this.filter_query = filter_query
      this.order_query = order_query
      await this.fetchItems()
    },
    async fetchItems() {
      let request = `/users-api/listing/users?page=${this.page}&items=${this.items_per_page}`
      let count_request = `/users-api/listing/users/count`

      if (this.filter_query) {
        request += `&${this.filter_query}`
        count_request += `?${this.filter_query}`
      }

      if (this.order_query) {
        request += `&${this.order_query}`
      }

      this.users = await this.$axios.$get(request)
      this.total_items = await this.$axios.$get(count_request)
    },
    async updatePagination(page) {
      this.page = page
      await this.fetchItems()
    },
    async clickActive(row) {
      const user = await this.$axios.$get(`/users-api/user/${row.id}`)
      const { id, name, code, ...r } = row
      let output
      output = { ...r, service: user.service.id, roles: user.roles }
      await this.$axios.$put(`/users-api/user/${row.id}`, output)
    },
    row_click_action(row, column, event) {
      this.$router.push(`/users/user/${row.id}`)
    },
  },
}
</script>
