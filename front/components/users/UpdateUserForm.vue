<template>
  <div class="container">
    <div>
      <validation-observer ref="formObserver" v-slot="{ handleSubmit }">
        <!--        todo: make user edit form a component-->
        <form
          :key="formKey"
          @submit.prevent="handleSubmit(makeUpdate)"
          @reset.prevent="triggerReInit"
          novalidate
        >
          <div class="row content-section">
            <div class="col-md-6">
              <div>
                <base-input
                  label="Nom"
                  name="Nom"
                  required="required"
                  v-model="user.last_name"
                ></base-input>
              </div>
              <div>
                <base-input
                  label="Prénom"
                  name="Prénom"
                  required="required"
                  v-model="user.first_name"
                ></base-input>
              </div>
              <div>
                <base-input
                  label="Adresse"
                  name="Adresse"
                  required="required"
                  v-model="user.address"
                ></base-input>
              </div>
              <div>
                <base-input
                  label="Téléphone"
                  name="Téléphone"
                  required="required"
                  type="tel"
                  v-model="user.mobile_number"
                ></base-input>
              </div>
              <div>
                <base-input
                  label="Email"
                  name="Email"
                  required="required"
                  type="email"
                  v-model="user.email"
                  :disabled="disable_user_email"
                ></base-input>
              </div>
            </div>
            <div class="col-md-6">
              <div>
                <base-input
                  label="Service"
                  name="Service"
                  required="required"
                  :group="true"
                >
                  <el-select
                    filterable
                    v-model="user.service"
                    class="col-md-8 no-padding"
                    placeholder="-- Choisissez une valeur --"
                  >
                    <el-option
                      v-for="service in available_services"
                      :value="service.id"
                      :label="`${service.name} (${service.code})`"
                    ></el-option>
                  </el-select>
                  <slot name="append">
                    <div class="col-md-4">
                      <base-button
                        type="default"
                        class="float-md-right"
                        @click="createServiceModal = true"
                        >Créer service
                      </base-button>
                    </div>
                  </slot>
                </base-input>
              </div>
              <div>
                <base-input label="Rôles" name="Rôles" required="required">
                  <el-select
                    filterable
                    multiple
                    v-model="user.roles"
                    placeholder="..."
                  >
                    <el-option
                      v-for="role in available_roles"
                      :value="role.code"
                      :label="`${role.name} (${role.code})`"
                      :key="role.code"
                    ></el-option>
                  </el-select>
                </base-input>
              </div>
              <div>
                <base-input
                  label="Matricule"
                  name="Matricule"
                  required="required"
                  v-model="user.registration_number"
                ></base-input>
              </div>
              <div>
                <div class="form-group">
                  <label class="form-control-label">Actif</label>
                  <div class="has-label">
                    <base-switch v-model="user.is_active"></base-switch>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row actions-zone">
            <div class="col-sm">
              <base-button
                type="secondary"
                native-type="reset"
                class="col-md-12"
                >Annuler</base-button
              >
            </div>
            <div class="col-sm">
              <base-button type="primary" native-type="submit" class="col-md-12"
                >Enregistrer</base-button
              >
            </div>
          </div>
        </form>
      </validation-observer>
      <modal :show.sync="createServiceModal">
        <create-user-service-form
          v-on:modal:cancel="hideCreateServiceModal"
          v-on:service:created="onServiceCreated"
        ></create-user-service-form>
      </modal>
    </div>
  </div>
</template>

<script>
import { Option, Select } from 'element-ui'
import { getRandomString } from '@/utils/string'
import CreateUserServiceForm from '@/components/users/services/CreateUserServiceForm'

export default {
  name: 'UpdateUserForm',
  components: {
    [Select.name]: Select,
    [Option.name]: Option,
    CreateUserServiceForm,
  },
  async fetch() {
    await this.fetchServicesList()
    this.available_roles = await this.$axios.$get(`/auth-api/roles`)
  },
  props: {
    user: Object,
  },
  data() {
    return {
      available_services: [],
      available_roles: [],
      formKey: getRandomString(15),
      createServiceModal: false,
      disable_user_email: true,
    }
  },
  methods: {
    async makeUpdate() {
      const { id, ...data } = this.user
      try {
        await this.$axios.$put(`/users-api/user/${this.user.id}`, data)
      } catch (exception) {
        console.error(exception)
        return
      }
      this.$emit('user:updated', this.user.id)
      this.formKey = getRandomString(15)
    },
    triggerReInit() {
      this.$emit('form:re-init')
      this.$refs.formObserver.reset()
    },
    hideCreateServiceModal() {
      this.createServiceModal = false
    },
    async fetchServicesList() {
      this.available_services = await this.$axios.$get(`/users-api/services`)
    },
    async onServiceCreated(serviceCode) {
      this.hideCreateServiceModal()
      await this.fetchServicesList()
      this.user.service = this.available_services.find(
        (service) => service.code === serviceCode
      ).id
    },
  },
}
</script>
