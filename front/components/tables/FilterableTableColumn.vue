<template>
  <el-table-column
        :render-header="renderFilterHeader"
        v-bind="$attrs"
    >
        <slot></slot>
  </el-table-column>
</template>

<script>
import {
  TableColumn,
} from "element-ui";

export default {
  name: "FilterableTableColumn",
  //props: { },
  components: {
    [TableColumn.name]: TableColumn
  },
  data() {
    return {
      
    };
  },
  methods: {
    // customise header table column
    renderFilterHeader(h,{column,$index}) {
        let self = this
        let result = h('i', {
            attrs: { class: 'fa fa-filter' },
            style: 'color: #1d4293; font-size: 9px; padding: 0px 0 0 6px;'
        })
        column.filters = [] // very important, keep this
        return h('div', { style: 'display: inline-flex; align-items: center;' }, [
            h('div', null, column.label), result
        ])
    },
  },
};
</script>
