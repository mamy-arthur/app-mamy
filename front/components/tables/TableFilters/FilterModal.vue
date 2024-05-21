<template>
  <div class="container">
    <validation-observer ref="filterObserver" v-slot="{ handleSubmit }">
      <form
        :key="formKey"
        @submit.prevent="handleSubmit(makeFilter)"
        novalidate
      >
        <div class="row content-section">
          <div class="col-md-12 mb-2">
            <h3>{{ filter.label }}</h3>
            <p>Veuillez saisir une valeur de recherche</p>
          </div>
          <div class="col-md-12">
            <div>
                
              <base-input
              v-show="!filter.choices"
                :name="filter.property"
                v-model="filter.value"
              ></base-input>

              <base-input :name="filter.property" v-show="filter.choices">
                <el-select
                  filterable
                  v-model="filter.value"
                  placeholder="-- Choisissez une valeur --"
                >
                  <el-option
                    v-for="item in filter.choices"
                    :value="item"
                    :key="item"
                    :label="item"
                  ></el-option>
                </el-select>
              </base-input>

            </div>
          </div>
        </div>
        <div class="row actions-zone">
          <div class="col-sm">
            <base-button
              type="secondary"
              native-type="reset"
              @click="makeCancel"
              class="col-md-12"
            >
              Annuler
            </base-button>
          </div>
          <div class="col-sm">
            <base-button type="primary" native-type="submit" class="col-md-12"
              >Valider</base-button
            >
          </div>
        </div>
      </form>
    </validation-observer>
  </div>
</template>

<script>
import { Option, Select } from "element-ui";
import { getRandomString } from "@/utils/string";
import WithFormConstraints from "@/mixins/with-form-constraints.mixin";

export default {
  name: "FilterModal",
  props: ["filter"],
  //components: { Select, Option },
  components: { [Select.name]: Select, [Option.name]: Option },
  data: () => ({
    formKey: getRandomString(15),
  }),
  mixins: [WithFormConstraints],
  methods: {
    makeFilter() {
      this.$emit("filter:apply", this.filter);
      this.formKey = getRandomString(15);
    },
    makeCancel() {
        this.$emit("filter:cancel");
    },
  },
};
</script>
