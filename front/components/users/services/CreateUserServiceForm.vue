<template>
  <div>
    <validation-observer ref="formObserver" v-slot="{ handleSubmit }">
      <form
        :key="formKey"
        ref="creationFrom"
        @submit.prevent="handleSubmit(createService)"
        @reset.prevent="triggerCancel"
        novalidate
      >
        <div class="content-section">
          <div>
            <base-input
              label="Nom du service"
              name="Nom du service"
              required="required"
              v-model="service.name"
            ></base-input>
          </div>
          <div>
            <base-input
              label="Code"
              name="Code"
              required="required"
              v-model="service.code"
            ></base-input>
          </div>
        </div>
        <div class="modal-footer actions-zone">
          <base-button type="secondary" native-type="reset" class="col-md-3"
            >Annuler</base-button
          >
          <base-button type="primary" native-type="submit" class="col-md-5 col"
            >Créer le service</base-button
          >
        </div>
      </form>
    </validation-observer>
  </div>
</template>

<script>
import { getRandomString } from '@/utils/string'

const emptyService = {
  name: null,
  code: null,
}

export default {
  name: 'CreateUserServiceForm',
  data() {
    return {
      service: { ...emptyService },
      formKey: getRandomString(15),
    }
  },
  methods: {
    triggerCancel() {
      this.$emit('modal:cancel')
    },
    async createService() {
      await this.$axios.$post('/users-api/service', this.service)
      this.$emit('service:created', this.service.code)
      this.service = { ...emptyService }
      this.formKey = getRandomString(15)
    },
  },
}
</script>
